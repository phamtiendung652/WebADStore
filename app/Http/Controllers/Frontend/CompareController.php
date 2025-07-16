<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Specification;

class CompareController extends Controller
{
     public function index(Request $request)
    {
        // Lấy danh sách ID sản phẩm từ query parameter
        // Đây là cách bạn có thể nhận IDs nếu gửi qua URL (ví dụ: /compare?ids=1,2,3)
        // Tuy nhiên, nếu bạn chỉ dựa vào localStorage và JS, thì phần này có thể không cần thiết
        // Nếu muốn bảo mật hơn, bạn có thể gửi IDs qua AJAX POST request từ frontend.
        $productIds = [];
        if ($request->has('ids')) {
            $productIds = explode(',', $request->input('ids'));
            // Lọc bỏ các giá trị không phải số hoặc trùng lặp
            $productIds = array_filter(array_unique(array_map('intval', $productIds)));
        } else {
            // Hoặc nếu bạn muốn thử nghiệm, có thể dùng tạm một số ID cố định
            // $productIds = [1, 2];
        }


        $products = collect(); // Khởi tạo một Collection rỗng
        if (!empty($productIds)) {
            // Lấy thông tin sản phẩm và thông số kỹ thuật liên quan
            $products = Product::whereIn('id', $productIds)
                               ->with('specification') // Eager load bảng specifications
                               ->get();
        }

        // Truyền dữ liệu sản phẩm sang view
        $viewData = [
            'products' => $products,
            'title_page' => 'So sánh sản phẩm'
        ];

        return view('frontend.pages.compare.index', $viewData);
    }
}
