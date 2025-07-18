<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Requests\AdminRequestProduct;
use App\Models\Category;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\Keyword;
use App\Models\Specification;
use App\Models\ProductVariant; // Import ProductVariant model

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category:id,c_name');
        if ($id = $request->id) $products->where('id', $id);
        if ($name = $request->name) $products->where('pro_name','like', '%'.$name.'%');
        if ($category = $request->category) $products->where('pro_category_id',$category);

        $products = $products->orderByDesc('id')->paginate(10);
        $categories = Category::all();
        $viewData = [
            'products'   => $products,
            'categories' => $categories,
            'query'      => $request->query()
        ];

        return view('admin.product.index', $viewData);
    }

    public function create()
    {
        $categories = Category::all();
        $attributeOld = [];
        $keywordOld   = [];

        $attributes =  $this->syncAttributeGroup();
        $keywords   = Keyword::all();

        $supplier = Supplier::all();

        // When creating, there's no existing product or specification data
        // So we don't pass $product->specification or $product->variants
        return view('admin.product.create', compact('categories','attributeOld','attributes','keywords','keywordOld','supplier'));
    }

    public function store(AdminRequestProduct $request)
    {
        // Exclude specification and variant fields from product data
        $data = $request->except('_token','pro_avatar','attribute','keywords','file','pro_sale','pro_file',
                                 'sp_cpu', 'sp_gpu', 'sp_ram', 'sp_storage', 'sp_display',
                                 'variants', 'deleted_variants'); // Thêm 'variants', 'deleted_variants'
        $data['pro_slug']     = Str::slug($request->pro_name);
        $data['created_at']   = Carbon::now();
        $data['pro_active'] = $request->has('pro_active') ? 1 : 0;
        $data['pro_hot'] = $request->has('pro_hot') ? 1 : 0;
        $data['pro_pay'] = $request->has('pro_pay') ? 1 : 0;

        if ($request->pro_sale)
        {
            $data['pro_sale'] = $request->pro_sale;
        }

        if ($request->pro_avatar) {
            $image = upload_image('pro_avatar');
            if ($image['code'] == 1)
                $data['pro_avatar'] = $image['name'];
        }

        if ($request->pro_file) {
            $image = upload_image('pro_file');
            if ($image['code'] == 1)
                $data['pro_file'] = $image['name'];
        }

        $id = Product::insertGetId($data);
        if ($id) {
            $this->syncAttribute($request->attribute, $id);
            $this->syncKeyword($request->keywords, $id);
            if ($request->file) {
                $this->syncAlbumImageAndProduct($request->file, $id);
            }
            $this->syncSpecification($request, $id);
            $this->syncProductVariants($request, $id); // Xử lý biến thể
        }

        return redirect()->back()->with('success', 'Sản phẩm đã được thêm thành công!');
    }

    public function edit($id)
    {
        // Eager load specification and variants
        $product = Product::with('specification', 'variants')->findOrFail($id);
        $categories = Category::all();
        $attributes =  $this->syncAttributeGroup();
        $keywords   = Keyword::all();
        $supplier = Supplier::all();

        $attributeOld = \DB::table('products_attributes')
            ->where('pa_product_id', $id)
            ->pluck('pa_attribute_id')
            ->toArray();

        $keywordOld = \DB::table('products_keywords')
            ->where('pk_product_id', $id)
            ->pluck('pk_keyword_id')
            ->toArray();

        if (!$attributeOld)  $attributeOld = [];
        if (!$keywordOld)    $keywordOld = [];

        $images = \DB::table('product_images')
            ->where("pi_product_id", $id)
            ->get();

        $viewData = [
            'categories'    => $categories,
            'product'       => $product,
            'attributes'    => $attributes,
            'attributeOld'  => $attributeOld,
            'keywords'      => $keywords,
            'supplier'      => $supplier,
            'keywordOld'    => $keywordOld,
            'images'        => $images ?? []
        ];

        return view('admin.product.update', $viewData);
    }

    public function update(AdminRequestProduct $request, $id)
    {
        $product                   = Product::find($id);
        if (!$product) {
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại.');
        }

        // Exclude specification and variant fields from product data
        $data                      = $request->except('_token','pro_avatar','attribute','keywords','file','pro_sale','pro_file',
                                 'sp_cpu', 'sp_gpu', 'sp_ram', 'sp_storage', 'sp_display',
                                 'variants', 'deleted_variants'); // Thêm 'variants', 'deleted_variants'
        $data['pro_slug']          = Str::slug($request->pro_name);
        $data['updated_at']        = Carbon::now();
        $data['pro_active'] = $request->has('pro_active') ? 1 : 0;
        $data['pro_hot'] = $request->has('pro_hot') ? 1 : 0;
        $data['pro_pay'] = $request->has('pro_pay') ? 1 : 0;

        if ($request->pro_sale)
        {
            $data['pro_sale'] = $request->pro_sale;
        }

        if ($request->pro_avatar) {
            $image = upload_image('pro_avatar');
            if ($image['code'] == 1)
                $data['pro_avatar'] = $image['name'];
        }

        if ($request->pro_file) {
            $image = upload_image('pro_file');
            if ($image['code'] == 1)
                $data['pro_file'] = $image['name'];
        }

        $update = $product->update($data);

        if ($update) {
            $this->syncAttribute($request->attribute, $id);
            $this->syncKeyword($request->keywords, $id);

            if ($request->file) {
                $this->syncAlbumImageAndProduct($request->file, $id);
            }
            $this->syncSpecification($request, $id);
            $this->syncProductVariants($request, $id); // Xử lý biến thể
        }

        return redirect()->back()->with('success', 'Sản phẩm đã được cập nhật thành công!');
    }

    public function syncAlbumImageAndProduct($files, $productID)
    {
        foreach ($files as $key => $fileImage) {
            $ext = $fileImage->getClientOriginalExtension();
            $extend = [
                'png','jpg','jpeg','PNG','JPG'
            ];

            if (!in_array($ext, $extend)) return false;

            $filename = date('Y-m-d__').Str::slug($fileImage->getClientOriginalName()).'.'.$ext;
            $path = public_path().'/uploads/'.date('Y/m/d/');
            if (!\File::exists($path)){
                mkdir($path, 0777, true);
            }

            $fileImage->move($path, $filename);
            \DB::table('product_images')
            ->insert([
                'pi_name' => $fileImage->getClientOriginalName(),
                'pi_slug' => $filename,
                'pi_product_id' => $productID,
                'created_at' => Carbon::now()
            ]);
        }
    }

    public function active($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->pro_active = ! $product->pro_active;
            $product->save();
        }
        return redirect()->back();
    }

    public function hot($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->pro_hot = ! $product->pro_hot;
            $product->save();
        }
        return redirect()->back();
    }

    private function syncKeyword($keywords, $idProduct)
    {
        \DB::table('products_keywords')->where('pk_product_id', $idProduct)->delete();

        if (!empty($keywords)) {
            $datas = [];
            foreach ($keywords as $keyword) {
                $datas[] = [
                    'pk_product_id' => $idProduct,
                    'pk_keyword_id' => $keyword
                ];
            }
            \DB::table('products_keywords')->insert($datas);
        }
    }

    public function delete($id)
    {
        $product = Product::find($id);
        if ($product) {
            // Xóa các dữ liệu liên quan trước
            Specification::where('sp_product_id', $id)->delete();
            ProductVariant::where('product_id', $id)->delete(); // Xóa biến thể
            // \DB::table('product_images')->where('pi_product_id', $id)->delete();
            \DB::table('products_attributes')->where('pa_product_id', $id)->delete();
            // \DB::table('products_keywords')->where('pk_product_id', $id)->delete();

            $product->delete();
        }

        return redirect()->back()->with('success', 'Sản phẩm đã được xóa thành công!');
    }

    public function deleteImage($imageID)
    {
        $image = \DB::table('product_images')->where('id', $imageID)->first();
        if ($image) {
            $path = public_path().'/uploads/'.date('Y/m/d', strtotime($image->created_at)) . '/' . $image->pi_slug;
            if (\File::exists($path)) {
                \File::delete($path);
            }
            \DB::table('product_images')->where('id', $imageID)->delete();
            return redirect()->back()->with('success', 'Ảnh đã được xóa thành công!');
        }
        return redirect()->back()->with('error', 'Không tìm thấy ảnh để xóa.');
    }

    protected function syncAttribute($attributes , $idProduct)
    {
        \DB::table('products_attributes')->where('pa_product_id', $idProduct)->delete();

        if (!empty($attributes)) {
            $datas = [];
            foreach ($attributes as $value) {
                $datas[] = [
                    'pa_product_id'   => $idProduct,
                    'pa_attribute_id' => $value
                ];
            }
            if (!empty($datas)) {
                \DB::table('products_attributes')->insert($datas);
            }
        }
    }

    protected function syncSpecification(Request $request, $productId)
    {
        $specificationData = $request->only('sp_cpu', 'sp_gpu', 'sp_ram', 'sp_storage', 'sp_display');

        $hasSpecificationData = false;
        foreach ($specificationData as $value) {
            if (!empty($value)) {
                $hasSpecificationData = true;
                break;
            }
        }

        if ($hasSpecificationData) {
            Specification::updateOrCreate(
                ['sp_product_id' => $productId],
                $specificationData
            );
        } else {
            Specification::where('sp_product_id', $productId)->delete();
        }
    }

    /**
     * Syncs (creates, updates, or deletes) product variants.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $productId
     * @return void
     */
    protected function syncProductVariants(Request $request, $productId)
    {
        $inputVariants = $request->input('variants', []);
        $deletedVariantIds = json_decode($request->input('deleted_variants', '[]'), true);

        // Xóa các biến thể đã được đánh dấu xóa
        if (!empty($deletedVariantIds)) {
            ProductVariant::whereIn('id', $deletedVariantIds)
                          ->where('product_id', $productId)
                          ->delete();
        }

        foreach ($inputVariants as $index => $variantData) {
            $variantId = $variantData['id'] ?? null;

            // Xử lý upload ảnh biến thể
            $variantImageName = null;
            if ($request->hasFile("variants.{$index}.variant_image")) {
                $imageFile = $request->file("variants.{$index}.variant_image");
                $ext = $imageFile->getClientOriginalExtension();
                $filename = date('Y-m-d__') . Str::slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $ext;
                $path = public_path() . '/uploads/variants/' . date('Y/m/d/'); // Thư mục riêng cho ảnh biến thể
                if (!\File::exists($path)) {
                    mkdir($path, 0777, true);
                }
                $imageFile->move($path, $filename);
                $variantImageName = date('Y/m/d/') . $filename; // Lưu đường dẫn tương đối
            }

            // Chuẩn bị dữ liệu cho biến thể
            $dataToSave = [
                'product_id'        => $productId,
                'variant_name'      => $variantData['variant_name'] ?? null,
                'color'             => $variantData['color'] ?? null,
                'sku'               => $variantData['sku'] ?? null,
                'price_adjustment'  => $variantData['price_adjustment'] ?? 0.00,
                'current_price'     => $variantData['current_price'] ?? 0.00,
                'quantity_in_stock' => $variantData['quantity_in_stock'] ?? 0,
                'is_active'         => isset($variantData['is_active']) ? 1 : 0,
            ];

            if ($variantImageName) {
                $dataToSave['variant_image'] = $variantImageName;
            }

            if ($variantId) {
                // Cập nhật biến thể hiện có
                $variant = ProductVariant::find($variantId);
                if ($variant) {
                    // Nếu có ảnh mới, xóa ảnh cũ (nếu có)
                    if ($variantImageName && $variant->variant_image) {
                        $oldImagePath = public_path('uploads/variants/' . $variant->variant_image);
                        if (\File::exists($oldImagePath)) {
                            \File::delete($oldImagePath);
                        }
                    }
                    $variant->update($dataToSave);
                }
            } else {
                // Tạo biến thể mới
                ProductVariant::create($dataToSave);
            }
        }
    }


    public function syncAttributeGroup()
    {
        $attributes     = Attribute::get();
        $groupAttribute = [];

        foreach ($attributes as $attribute) {
            $key = $attribute->gettype($attribute->atb_type)['name'];
            $groupAttribute[$key][] = $attribute->toArray();
        }

        return $groupAttribute;
    }

}