<form role="form" action="" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="col-sm-8">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Thông tin cơ bản</h3>
            </div>
            <div class="box-body">
                <div class="form-group ">
                    <label for="exampleInputEmail1">Tên sản phẩm</label>
                    <input type="text" class="form-control" name="pro_name" placeholder="Sản phẩm ...."
                        autocomplete="off" value="{{ $product->pro_name ?? old('pro_name') }}">
                    @if ($errors->first('pro_name'))
                        <span class="text-danger">{{ $errors->first('pro_name') }}</span>
                    @endif
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Giá sản phẩm</label>
                            <input type="text" name="pro_price"
                                value="{{ $product->pro_price ?? old('pro_price', 0) }}" class="form-control"
                                data-type="currency" placeholder="15.000.000">
                            @if ($errors->first('pro_price'))
                                <span class="text-danger">{{ $errors->first('pro_price') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Giảm giá</label>
                            <input type="number" name="pro_sale" value="{{ $product->pro_sale ?? old('pro_sale', 0) }}"
                                class="form-control" data-type="currency" placeholder="5">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Số lượng</label>
                            <input type="number" name="pro_number"
                                value="{{ $product->pro_number ?? old('pro_number', 0) }}" class="form-control"
                                placeholder="5">
                        </div>
                    </div>

                </div>

                {{-- NEW BLOCK FOR DETAILED SPECIFICATIONS --}}
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Thông số kỹ thuật</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="sp_cpu">CPU</label>
                                    <input type="text" class="form-control" name="sp_cpu"
                                        placeholder="Ví dụ: Intel Core i7"
                                        value="{{ $product->specification->sp_cpu ?? old('sp_cpu') }}">
                                    @if ($errors->first('sp_cpu'))
                                        <span class="text-danger">{{ $errors->first('sp_cpu') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="sp_gpu">GPU</label>
                                    <input type="text" class="form-control" name="sp_gpu"
                                        placeholder="Ví dụ: NVIDIA RTX 3060"
                                        value="{{ $product->specification->sp_gpu ?? old('sp_gpu') }}">
                                    @if ($errors->first('sp_gpu'))
                                        <span class="text-danger">{{ $errors->first('sp_gpu') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="sp_ram">RAM</label>
                                    <input type="text" class="form-control" name="sp_ram"
                                        placeholder="Ví dụ: 16GB DDR4"
                                        value="{{ $product->specification->sp_ram ?? old('sp_ram') }}">
                                    @if ($errors->first('sp_ram'))
                                        <span class="text-danger">{{ $errors->first('sp_ram') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="sp_storage">Bộ nhớ</label>
                                    <input type="text" class="form-control" name="sp_storage"
                                        placeholder="Ví dụ: 512GB SSD"
                                        value="{{ $product->specification->sp_storage ?? old('sp_storage') }}">
                                    @if ($errors->first('sp_storage'))
                                        <span class="text-danger">{{ $errors->first('sp_storage') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="sp_display">Màn hình</label>
                                    <input type="text" class="form-control" name="sp_display"
                                        placeholder="Ví dụ: 15.6 inch Full HD IPS"
                                        value="{{ $product->specification->sp_display ?? old('sp_display') }}">
                                    @if ($errors->first('sp_display'))
                                        <span class="text-danger">{{ $errors->first('sp_display') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- END NEW BLOCK FOR DETAILED SPECIFICATIONS --}}

                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">Quản lý biến thể sản phẩm</h3>
                        {{-- Nút này có thể thêm các trường biến thể động bằng JS --}}
                        <button type="button" id="add_variant_btn" class="btn btn-info btn-sm pull-right">
                            <i class="fa fa-plus"></i> Thêm biến thể
                        </button>
                    </div>
                    <div class="box-body" id="product_variants_container">
                        {{-- Các biến thể hiện có sẽ được render ở đây --}}
                        @if (isset($product) && $product->variants->isNotEmpty())
                            @foreach ($product->variants as $index => $variant)
                                <div class="variant-item"
                                    style="border: 1px solid #eee; padding: 15px; margin-bottom: 10px; border-radius: 5px;">
                                    <h4>Biến thể #{{ $index + 1 }}
                                        <button type="button"
                                            class="btn btn-danger btn-xs pull-right remove-variant-btn"
                                            data-variant-id="{{ $variant->id }}">Xóa</button>
                                    </h4>
                                    <input type="hidden" name="variants[{{ $index }}][id]"
                                        value="{{ $variant->id }}">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Tên biến thể</label>
                                                <input type="text"
                                                    name="variants[{{ $index }}][variant_name]"
                                                    class="form-control"
                                                    value="{{ old('variants.' . $index . '.variant_name', $variant->variant_name) }}"
                                                    placeholder="Ví dụ: Màu đen">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Màu sắc</label>
                                                <input type="text" name="variants[{{ $index }}][color]"
                                                    class="form-control"
                                                    value="{{ old('variants.' . $index . '.color', $variant->color) }}"
                                                    placeholder="Ví dụ: Đen">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Mã SKU</label>
                                                <input type="text" name="variants[{{ $index }}][sku]"
                                                    class="form-control"
                                                    value="{{ old('variants.' . $index . '.sku', $variant->sku) }}"
                                                    placeholder="Ví dụ: SP001-DEN">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Điều chỉnh giá</label>
                                                <input type="number"
                                                    name="variants[{{ $index }}][price_adjustment]"
                                                    class="form-control"
                                                    value="{{ old('variants.' . $index . '.price_adjustment', $variant->price_adjustment) }}"
                                                    step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Giá hiện tại</label>
                                                <input type="number"
                                                    name="variants[{{ $index }}][current_price]"
                                                    class="form-control"
                                                    value="{{ old('variants.' . $index . '.current_price', $variant->current_price) }}"
                                                    step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Số lượng trong kho</label>
                                                <input type="number"
                                                    name="variants[{{ $index }}][quantity_in_stock]"
                                                    class="form-control"
                                                    value="{{ old('variants.' . $index . '.quantity_in_stock', $variant->quantity_in_stock) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Ảnh biến thể</label>
                                        <input type="file" name="variants[{{ $index }}][variant_image]"
                                            class="form-control">
                                        @if ($variant->variant_image)
                                            <img src="{{ pare_url_file($variant->variant_image) }}"
                                                alt="Variant Image" class="img-thumbnail"
                                                style="width: 100px; height: 100px; object-fit: cover; margin-top: 5px;">
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                    name="variants[{{ $index }}][is_active]" value="1"
                                                    {{ $variant->is_active ? 'checked' : '' }}> Kích hoạt
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        {{-- Template cho biến thể mới (sẽ được clone bằng JS) --}}
                        <template id="variant_template">
                            <div class="variant-item"
                                style="border: 1px solid #eee; padding: 15px; margin-bottom: 10px; border-radius: 5px;">
                                <h4>Biến thể mới
                                    <button type="button"
                                        class="btn btn-danger btn-xs pull-right remove-variant-btn">Xóa</button>
                                </h4>
                                <input type="hidden" name="variants[__INDEX__][id]" value="">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Tên biến thể</label>
                                            <input type="text" name="variants[__INDEX__][variant_name]"
                                                class="form-control" placeholder="Ví dụ: Màu đen">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Màu sắc</label>
                                            <input type="text" name="variants[__INDEX__][color]"
                                                class="form-control" placeholder="Ví dụ: Đen">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Mã SKU</label>
                                            <input type="text" name="variants[__INDEX__][sku]"
                                                class="form-control" placeholder="Ví dụ: SP001-DEN">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Điều chỉnh giá</label>
                                            <input type="number" name="variants[__INDEX__][price_adjustment]"
                                                class="form-control" value="0.00" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Giá hiện tại</label>
                                            <input type="number" name="variants[__INDEX__][current_price]"
                                                class="form-control" value="0.00" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Số lượng trong kho</label>
                                            <input type="number" name="variants[__INDEX__][quantity_in_stock]"
                                                class="form-control" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Ảnh biến thể</label>
                                    <input type="file" name="variants[__INDEX__][variant_image]"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="variants[__INDEX__][is_active]"
                                                value="1" checked> Kích hoạt
                                        </label>
                                    </div>
                                </div> <!-- Đã sửa: Đảm bảo thẻ đóng </div> đúng cho form-group -->
                            </div>
                        </template>
                    </div>
                </div>

                <div class="form-group ">
                    <label for="exampleInputEmail1">Chi Tiết Thông Số</label>
                    <textarea name="pro_description" id="pro_description" class="form-control textarea" cols="5" rows="2"
                        autocomplete="off">{{ $product->pro_description ?? old('pro_description') }}</textarea>
                    @if ($errors->first('pro_description'))
                        <span class="text-danger">{{ $errors->first('pro_description') }}</span>
                    @endif
                </div>



                <div class="form-group ">
                    <label class="control-label">Danh mục <b class="col-red">(*)</b></label>
                    <select name="pro_category_id" class="form-control ">
                        <option value="">__Chọn__</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ ($product->pro_category_id ?? '') == $category->id ? "selected='selected'" : '' }}>
                                {{ $category->c_name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->first('pro_category_id'))
                        <span class="text-danger">{{ $errors->first('pro_category_id') }}</span>
                    @endif
                </div>

                <div class="form-group ">
                    <label class="control-label">Nhà Cung cấp <b class="col-red">(*)</b></label>
                    <select name="pro_supplier_id" class="form-control ">
                        <option value="">__Chọn__</option>
                        @foreach ($supplier as $item)
                            <option value="{{ $item->id }}"
                                {{ ($product->pro_supplier_id ?? 0) == $item->id ? "selected='selected'" : '' }}>
                                {{ $item->sl_name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->first('pro_supplier_id'))
                        <span class="text-danger">{{ $errors->first('pro_supplier_id') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Thuộc tính</h3>
            </div>
            <div class="box-body">
                @foreach ($attributes as $key => $attribute)
                    <div class="form-group col-sm-3">
                        <h4 style="border-bottom: 1px solid #dedede;padding-bottom: 10px;">{{ $key }}</h4>
                        @foreach ($attribute as $item)
                            <div class="radio">
                                <label>
                                    <input type="radio" name="attribute[{{ $key }}]"
                                        {{ in_array($item['id'], $attributeOld) ? 'checked' : '' }}
                                        value="{{ $item['id'] }}"> {{ $item['atb_name'] }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <hr>
        </div>
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Nội dung</h3>
            </div>
            <div class="box-body">
                <div class="form-group ">
                    <label for="exampleInputEmail1">Content</label>
                    <textarea name="pro_content" id="pro_content" class="form-control textarea" cols="5" rows="2">{{ $product->pro_content ?? '' }}</textarea>

                    @if ($errors->first('pro_content'))
                        <span class="text-danger">{{ $errors->first('pro_content') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Ảnh đại diện</h3>
            </div>
            <div class="box-body block-images">
                <div style="margin-bottom: 10px">
                    <img src="{{ pare_url_file($product->pro_avatar ?? '') ?? '/images/no-image.jpg' }}"
                        onerror="this.onerror=null;this.src='/images/no-image.jpg';" alt=""
                        class="img-thumbnail" style="width: 200px;height: 200px;">
                </div>
                <div style="position:relative;"> <a class="btn btn-primary" href="javascript:;"> Choose File...
                        <input type="file"
                            style="position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:&quot;progid:DXImageTransform.Microsoft.Alpha(Opacity=0)&quot;;opacity:0;background-color:transparent;color:transparent;"
                            name="pro_avatar" size="40" class="js-upload"> </a> &nbsp; <span
                        class="label label-info" id="upload-file-info"></span> </div>
            </div>
        </div>
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Thao Tác</h3>
            </div>
            <div class="box-body ">
                <div class="box-footer text-center">
                    <a href="{{ route('admin.product.index') }}" class="btn btn-default"><i
                            class="fa fa-arrow-left"></i> Cancel</a>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>
                        {{ isset($product) ? 'Cập nhật' : 'Thêm mới' }} </button>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Tải các thư viện JS/CSS cần thiết trước script chính của bạn --}}
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<!-- Thêm Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.7/js/fileinput.js" type="text/javascript">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.7/themes/fa/theme.js"
    type="text/javascript"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.7/css/fileinput.css" media="all"
    rel="stylesheet" type="text/css" />

<script>
    // Đặt tất cả các script liên quan đến DOM và jQuery vào trong $(document).ready()
    $(document).ready(function() {
        // Khởi tạo CKEditor
        // Đảm bảo hàm ckeditor() được định nghĩa và có sẵn
        if (typeof ckeditor === 'function') {
            ckeditor('pro_content');
            ckeditor('pro_description');
        } else {
            console.warn(
                "CKEditor function 'ckeditor()' not found. Please ensure CKEditor is loaded correctly.");
        }


        // Script để hiển thị tên file khi chọn ảnh đại diện
        $(document).on('change', '.js-upload', function() {
            var fileName = $(this).val().split('\\').pop();
            $('#upload-file-info').html(fileName);
        });

        // Khởi tạo fileinput cho album ảnh
        if ($.fn.fileinput) {
            $("#images").fileinput({
                uploadUrl: "#", // Không cần upload URL nếu bạn xử lý bằng Laravel Controller
                enableResumableUpload: true,
                maxFileCount: 10, // Giới hạn số lượng ảnh tải lên
                showUpload: false, // Ẩn nút upload
                showRemove: false, // Ẩn nút remove
                initialPreviewAsData: true,
                overwriteInitial: false,
            });
        } else {
            console.error(
                "Lỗi: jQuery FileInput plugin không được tải hoặc không khả dụng. Hãy kiểm tra lại các file JS của Bootstrap và FileInput."
            );
        }

        // Logic cho việc thêm/xóa biến thể sản phẩm
        // Đảm bảo biến $product được truyền vào view và có thuộc tính variants
        let variantIndex = @json(isset($product) && $product->variants->isNotEmpty() ? $product->variants->count() : 0);

        $('#add_variant_btn').on('click', function() {
            const template = document.getElementById('variant_template');
            const container = document.getElementById('product_variants_container');

            if (!template || !container) {
                console.error("Template or container for variants not found.");
                return;
            }

            // Get the content from the template
            const clone = template.content.cloneNode(true);
            const newVariantItem = clone.querySelector(
                '.variant-item'); // Get the main div of the variant

            // Update names and IDs within the cloned content
            $(newVariantItem).find('[name*="__INDEX__"]').each(function() {
                const currentName = $(this).attr('name');
                if (currentName) {
                    $(this).attr('name', currentName.replace(/__INDEX__/, variantIndex));
                }
                const currentId = $(this).attr('id');
                if (currentId) {
                    $(this).attr('id', currentId.replace(/__INDEX__/, variantIndex));
                }
            });

            // Update the title for the new variant
            $(newVariantItem).find('h4').text('Biến thể mới #' + (variantIndex + 1));

            container.appendChild(newVariantItem); // Append the modified clone

            variantIndex++;
        });

        // Handle remove variant button (sử dụng event delegation với jQuery)
        $('#product_variants_container').on('click', '.remove-variant-btn', function(event) {
            if (confirm('Bạn có chắc chắn muốn xóa biến thể này không?')) {
                const variantItem = $(this).closest('.variant-item');
                const variantId = $(this).data('variantId');

                if (variantId) {
                    const form = $(this).closest('form');
                    let deletedVariantsInput = form.find('input[name="deleted_variants"]');
                    if (deletedVariantsInput.length === 0) {
                        deletedVariantsInput = $(
                            '<input type="hidden" name="deleted_variants" value="[]">');
                        form.append(deletedVariantsInput);
                    }
                    let currentDeleted = JSON.parse(deletedVariantsInput.val());
                    currentDeleted.push(variantId);
                    deletedVariantsInput.val(JSON.stringify(currentDeleted));
                }
                variantItem.remove();
            }
        });

        // Function to handle file input name changes (for dynamic variants)
        $(document).on('change', 'input[type="file"][name^="variants"]', function() {
            var fileName = $(this).val().split('\\').pop();
            console.log('Selected file for variant:', fileName);
        });
    });
</script>
