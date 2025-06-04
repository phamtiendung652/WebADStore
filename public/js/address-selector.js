// public/js/address-selector.js

(function() {
    const url = "https://raw.githubusercontent.com/giaodienblog/provinces/refs/heads/main/district.json";
    let data = [];

    const citySelect = document.getElementById("city");
    const districtSelect = document.getElementById("district");
    const wardSelect = document.getElementById("ward");
    const detailedAddressInput = document.getElementById("detailed_address"); // Input số nhà/tên đường
    const fullAddressOutput = document.getElementById("fullAddressOutput");   // Input hidden để gộp địa chỉ

    const callAPI = () => {
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(responseData => {
                data = responseData;
                renderData(data, "city", "Chọn tỉnh / TP");

                // Nếu bạn có logic để khôi phục địa chỉ đã lưu của người dùng
                // từ database để tự động chọn các dropdown, bạn sẽ cần thêm vào đây.
                // Điều này yêu cầu lưu trữ ID của Tỉnh/Huyện/Xã trong DB.
            })
            .catch(error => {
                console.error("Có lỗi khi lấy dữ liệu tỉnh/thành phố:", error);
            });
    };

    const renderData = (array, selectId, defaultText = "Chọn") => {
        const selectElement = document.getElementById(selectId);
        let options = `<option value="" selected>${defaultText}</option>`;
        array.forEach(element => {
            options += `<option data-id="${element.code}" value="${element.name}">${element.name}</option>`;
        });
        selectElement.innerHTML = options;
    };

    const updateCombinedAddress = () => {
        const selectedWardText = wardSelect.value ? wardSelect.options[wardSelect.selectedIndex].textContent : '';
        const selectedDistrictText = districtSelect.value ? districtSelect.options[districtSelect.selectedIndex].textContent : '';
        const selectedCityText = citySelect.value ? citySelect.options[citySelect.selectedIndex].textContent : '';
        const detailedAddressText = detailedAddressInput.value.trim(); // Lấy giá trị từ input số nhà/tên đường

        let addressParts = [];

        // Đảm bảo thứ tự và chỉ thêm vào nếu có giá trị
        if (detailedAddressText) {
            addressParts.push(detailedAddressText);
        }
        if (selectedWardText) {
            addressParts.push(selectedWardText);
        }
        if (selectedDistrictText) {
            addressParts.push(selectedDistrictText);
        }
        if (selectedCityText) {
            addressParts.push(selectedCityText);
        }

        // Gộp tất cả các phần lại thành một chuỗi duy nhất, phân cách bằng dấu phẩy
        const combinedAddress = addressParts.join(', ');
        fullAddressOutput.value = combinedAddress; // Gán vào trường input ẩn
    };

    // Lắng nghe sự kiện 'change' trên các thẻ select để cập nhật địa chỉ
    citySelect.addEventListener("change", function() {
        const selectedOption = this.options[this.selectedIndex];
        const cityId = parseInt(selectedOption.dataset.id);

        if (data.find((d) => d.code === cityId)) {
            renderData(data.find((d) => d.code === cityId).districts, "district", "Chọn quận huyện");
        } else {
            districtSelect.innerHTML = '<option value="" selected>Chọn quận huyện</option>';
        }
        wardSelect.innerHTML = '<option value="" selected>Chọn phường xã</option>';
        updateCombinedAddress(); // Cập nhật địa chỉ sau khi thay đổi Tỉnh/TP
    });

    districtSelect.addEventListener("change", function() {
        const selectedOption = this.options[this.selectedIndex];
        const districtId = parseInt(selectedOption.dataset.id);

        const selectedCityOption = citySelect.options[citySelect.selectedIndex];
        const cityId = parseInt(selectedCityOption.dataset.id);

        const selectedCity = data.find((d) => d.code === cityId);
        if (selectedCity) {
            const selectedDistrict = selectedCity.districts.find((d) => d.code === districtId);
            if (selectedDistrict) {
                renderData(selectedDistrict.wards, "ward", "Chọn phường xã");
            } else {
                wardSelect.innerHTML = '<option value="" selected>Chọn phường xã</option>';
            }
        }
        updateCombinedAddress(); // Cập nhật địa chỉ sau khi thay đổi Quận/Huyện
    });

    wardSelect.addEventListener("change", function() {
        updateCombinedAddress(); // Cập nhật địa chỉ sau khi thay đổi Phường/Xã
    });

    // Lắng nghe sự kiện 'input' trên trường số nhà/tên đường để cập nhật địa chỉ ngay lập tức
    detailedAddressInput.addEventListener("input", function() {
        updateCombinedAddress();
    });


    // Khởi tạo API khi tài liệu sẵn sàng và cập nhật địa chỉ lần đầu
    document.addEventListener('DOMContentLoaded', function() {
        callAPI();
        // Quan trọng: Gọi hàm gộp địa chỉ ngay khi tải trang
        // để gán giá trị ban đầu (từ get_data_user('web', 'address'))
        // vào trường input hidden, nếu có.
        updateCombinedAddress();
    });

    // Xuất hàm updateCombinedAddress ra global scope để có thể gọi từ script khác (modal)
    window.updateCombinedAddress = updateCombinedAddress;

})();