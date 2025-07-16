document.addEventListener('DOMContentLoaded', function() {
    const compareButtons = document.querySelectorAll('.btn-compare');
    const compareListKey = 'productCompareList'; // Key để lưu trong localStorage
    const maxCompareItems = 2; // Giới hạn số lượng sản phẩm so sánh

    // Hàm để lấy danh sách ID sản phẩm từ localStorage
    function getCompareList() {
        const list = localStorage.getItem(compareListKey);
        return list ? JSON.parse(list) : [];
    }

    // Hàm để lưu danh sách ID sản phẩm vào localStorage
    function saveCompareList(list) {
        localStorage.setItem(compareListKey, JSON.stringify(list));
    }

    // Hàm để cập nhật trạng thái nút (ví dụ: đổi text, màu)
    function updateButtonState(button, productId, currentList) {
        if (currentList.includes(productId)) {
            button.classList.add('btn-added-compare'); // Thêm class mới
            button.innerHTML = '<i class="la la-check"></i> Đã thêm';
        } else {
            button.classList.remove('btn-added-compare');
            button.innerHTML = '<i class="la la-plus"></i> So sánh';
        }
    }

    // Khởi tạo trạng thái nút khi tải trang
    compareButtons.forEach(button => {
        const productId = parseInt(button.dataset.id);
        const currentList = getCompareList();
        updateButtonState(button, productId, currentList);
    });

    // Xử lý sự kiện click cho nút So sánh
    compareButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = parseInt(this.dataset.id); // Lấy ID sản phẩm từ data-id
            let currentList = getCompareList();

            if (currentList.includes(productId)) {
                // Nếu sản phẩm đã có trong danh sách, xóa nó đi
                currentList = currentList.filter(id => id !== productId);
                alert('Sản phẩm đã được xóa khỏi danh sách so sánh!');
            } else {
                // Nếu sản phẩm chưa có, thêm vào danh sách (kiểm tra giới hạn)
                if (currentList.length < maxCompareItems) {
                    currentList.push(productId);
                    alert('Sản phẩm đã được thêm vào danh sách so sánh!');
                } else {
                    alert(`Bạn chỉ có thể so sánh tối đa ${maxCompareItems} sản phẩm.`);
                    return; // Không làm gì thêm nếu vượt quá giới hạn
                }
            }

            saveCompareList(currentList); // Lưu danh sách đã cập nhật
            updateButtonState(this, productId, currentList); // Cập nhật trạng thái nút

            // Tùy chọn: Chuyển hướng đến trang so sánh nếu đủ sản phẩm
            if (currentList.length === maxCompareItems) {
                 if (confirm('Đã đủ sản phẩm để so sánh. Bạn có muốn chuyển đến trang so sánh không?')) {
                     window.location.href = URL_COMPARE; // Đường dẫn đến trang so sánh
                 }
            }
        });
    });

    // Tùy chọn: Thêm một nút hoặc link để truy cập trang so sánh (ví dụ ở header)
    // Ví dụ:
    const goToComparePageBtn = document.getElementById('goToComparePage');
    if (goToComparePageBtn) {
        goToComparePageBtn.addEventListener('click', function() {
            const currentList = getCompareList();
            if (currentList.length >= 1) { // Có ít nhất 1 sản phẩm để so sánh
                window.location.href = URL_COMPARE;
            } else {
                alert('Vui lòng chọn ít nhất 1 sản phẩm để so sánh.');
            }
        });
    }
});