<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\WarehouseDetail;
use Illuminate\Http\Request;

class ProductInventoryController extends Controller
{

    /**
     * Lấy số lượng tồn kho của một sản phẩm để kiểm tra.
     *
     * @param int $productId ID của sản phẩm bạn muốn kiểm tra
     * @return int Số lượng tồn kho của sản phẩm, hoặc 0 nếu không tìm thấy
     */
    public function getProductStockQuantity(int $productId): int
    {
        // Sử dụng phương thức sum() để tính tổng quantity cho product_id đó.
        // Điều này rất quan trọng nếu một sản phẩm có thể xuất hiện nhiều lần
        // trong bảng warehousedetails (ví dụ: ở các kho khác nhau).
        $availableStock = WarehouseDetail::where('whd_product_id', $productId)->sum('whd_qty');

        // Trả về số lượng tồn kho. Nếu không tìm thấy bản ghi nào, sum() sẽ trả về 0.
        return $availableStock;
    }

    /**
     * Ví dụ về cách sử dụng hàm kiểm tra tồn kho khi thêm/cập nhật giỏ hàng.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleCartUpdate(Request $request)
    {
        $productId = $request->input('whd_product_id');
        $requestedQuantity = (int) $request->input('whd_qty'); // Số lượng người dùng muốn thêm/cập nhật

        // Bước 1: Lấy số lượng tồn kho từ hàm của chúng ta
        $stockQuantity = $this->getProductStockQuantity($productId);

        // Bước 2: Lấy số lượng hiện có của sản phẩm trong giỏ hàng (giả định lưu session)
        $cart = session()->get('cart', []);
        $currentQuantityInCart = isset($cart[$productId]) ? $cart[$productId]['whd_qty'] : 0;

        // Bước 3: Tính tổng số lượng nếu thêm vào giỏ
        $newTotalQuantityInCart = $currentQuantityInCart + $requestedQuantity;

        // Bước 4: Kiểm tra điều kiện
        if ($newTotalQuantityInCart > $stockQuantity) {
            return response()->json([
                'message' => 'Số lượng yêu cầu vượt quá số lượng tồn kho hiện có (' . $stockQuantity . ').',
                'status' => 'error',
                'available_stock' => $stockQuantity
            ], 400); // 400 Bad Request
        }

        // Nếu mọi thứ OK, tiến hành cập nhật giỏ hàng
        // ... (Logic cập nhật giỏ hàng thực tế của bạn ở đây)
        $cart[$productId] = [
            'whd_product_id' => $productId,
            'whd_qty' => $newTotalQuantityInCart,
            // ... các thông tin khác của sản phẩm
        ];
        session()->put('cart', $cart);


        return response()->json([
            'message' => 'Cập nhật giỏ hàng thành công.',
            'status' => 'success',
            'current_cart_quantity' => $newTotalQuantityInCart
        ]);
    }
    /**
     * Lấy số lượng tồn kho của một sản phẩm cụ thể.
     *
     * @param int $productId ID của sản phẩm
     * @return \Illuminate\Http\JsonResponse
     */
    // public function checkProductQuantity($productId)
    // {
    //     // Cách 1: Lấy tổng số lượng của sản phẩm từ bảng warehousedetails
    //     // Điều này giả định một product_id có thể xuất hiện nhiều lần (ví dụ: ở các kho khác nhau)
    //     $totalQuantity = WarehouseDetail::where('product_id', $productId)->sum('quantity');

    //     if (is_null($totalQuantity)) {
    //         return response()->json([
    //             'message' => 'Sản phẩm không tồn tại trong kho hoặc chưa có dữ liệu tồn kho.',
    //             'product_id' => $productId,
    //             'quantity' => 0
    //         ], 404);
    //     }

    //     return response()->json([
    //         'message' => 'Số lượng tồn kho của sản phẩm đã cho.',
    //         'product_id' => $productId,
    //         'quantity' => $totalQuantity
    //     ]);
    // }

    // /**
    //  * Lấy số lượng tồn kho của tất cả các sản phẩm.
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function getAllProductQuantities()
    // {
    //     // Lấy tổng số lượng cho mỗi product_id
    //     $productQuantities = WarehouseDetail::selectRaw('product_id, SUM(quantity) as total_quantity')
    //         ->groupBy('product_id')
    //         ->get();

    //     if ($productQuantities->isEmpty()) {
    //         return response()->json([
    //             'message' => 'Không có dữ liệu tồn kho nào.',
    //             'data' => []
    //         ], 404);
    //     }

    //     return response()->json([
    //         'message' => 'Danh sách số lượng tồn kho của các sản phẩm.',
    //         'data' => $productQuantities
    //     ]);
    // }
}
