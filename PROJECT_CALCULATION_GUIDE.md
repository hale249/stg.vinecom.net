# Hướng Dẫn Tính Toán Dự Án

## Tổng Quan

Hệ thống đã được cải thiện với logic tính toán tự động cho 3 trường quan trọng trong dự án:

1. **Mục tiêu dự án (Target Amount)** - Tổng số tiền cần huy động
2. **Số lượng chia sẻ (Share Count)** - Tổng số suất đầu tư
3. **Giá mỗi suất (Share Amount)** - Giá của mỗi suất đầu tư

## Cách Sử Dụng

### 1. Form Tạo/Chỉnh Sửa Dự Án

#### Các Mode Tính Toán

**Mode 1: Mục tiêu + Số lượng chia sẻ**
- Nhập: Mục tiêu dự án + Số lượng chia sẻ
- Hệ thống tự động tính: Giá mỗi suất = Mục tiêu ÷ Số lượng chia sẻ

**Mode 2: Mục tiêu + Giá mỗi suất**
- Nhập: Mục tiêu dự án + Giá mỗi suất
- Hệ thống tự động tính: Số lượng chia sẻ = Mục tiêu ÷ Giá mỗi suất (làm tròn)

**Mode 3: Số lượng chia sẻ + Giá mỗi suất**
- Nhập: Số lượng chia sẻ + Giá mỗi suất
- Hệ thống tự động tính: Mục tiêu dự án = Số lượng chia sẻ × Giá mỗi suất

#### Tính Năng Tự Động

- **Tính toán real-time**: Khi nhập giá trị, hệ thống tự động tính toán sau 0.5 giây
- **Nút tính toán**: Nhấn nút "Tính toán" để tính toán ngay lập tức
- **Tóm tắt tính toán**: Hiển thị công thức và kết quả tính toán
- **Cảnh báo**: Thông báo khi có chênh lệch giữa giá trị nhập và tính toán

### 2. Ví Dụ Thực Tế

#### Ví dụ 1: Dự án 20 tỷ VNĐ
```
Mục tiêu dự án: 20,000,000,000 VNĐ
Số lượng chia sẻ: 200 suất
→ Giá mỗi suất: 100,000,000 VNĐ
```

#### Ví dụ 2: Dự án với giá suất cố định
```
Mục tiêu dự án: 15,000,000,000 VNĐ
Giá mỗi suất: 50,000,000 VNĐ
→ Số lượng chia sẻ: 300 suất
```

### 3. Hiển Thị Project Detail

Khi người dùng đã đầu tư một phần, hệ thống hiển thị:

- **Tiến độ đầu tư**: 78% (tự động tính)
- **Mục tiêu dự án**: 20,000,000,000 VNĐ
- **Số tiền còn lại**: 4,400,000,000 VNĐ (44 suất × 100,000,000)
- **Đã đầu tư**: 15,600,000,000 VNĐ (156 suất × 100,000,000)

## Công Thức Tính Toán

### Backend (Model Project)

```php
// Mục tiêu dự án
public function getTargetAmountAttribute()
{
    return $this->share_count * $this->share_amount;
}

// Số tiền còn lại
public function getRemainingAmountAttribute()
{
    return $this->available_share * $this->share_amount;
}

// Số tiền đã đầu tư
public function getInvestedAmountAttribute()
{
    return ($this->share_count - $this->available_share) * $this->share_amount;
}

// Tiến độ đầu tư (%)
public function getInvestmentProgressAttribute()
{
    if ($this->share_count == 0) return 0;
    return round((($this->share_count - $this->available_share) / $this->share_count) * 100, 2);
}
```

### Frontend (JavaScript)

```javascript
// Tính giá mỗi suất
share_amount = target_amount / share_count

// Tính số lượng chia sẻ
share_count = Math.round(target_amount / share_amount)

// Tính mục tiêu dự án
target_amount = share_count * share_amount
```

## Lưu Ý Quan Trọng

### 1. Tính Nhất Quán
- Hệ thống luôn đảm bảo: `target_amount = share_count × share_amount`
- Khi lưu dự án, backend sẽ tự động điều chỉnh để đảm bảo tính nhất quán

### 2. Làm Tròn Số
- Khi tính số lượng chia sẻ từ mục tiêu và giá suất, hệ thống sẽ làm tròn
- Ví dụ: 20,000,000,000 ÷ 50,000,000 = 400 suất (chính xác)
- Ví dụ: 15,000,000,000 ÷ 50,000,000 = 300 suất (chính xác)

### 3. Validation
- Hệ thống kiểm tra và cảnh báo khi có chênh lệch
- Hiển thị thông báo rõ ràng về sự khác biệt

## Cách Setup Hợp Lý

### Bước 1: Chọn Mode Tính Toán
1. Vào form tạo/chỉnh sửa dự án
2. Chọn mode tính toán phù hợp với nhu cầu

### Bước 2: Nhập Giá Trị
1. Nhập 2 trong 3 giá trị theo mode đã chọn
2. Hệ thống tự động tính giá trị còn lại

### Bước 3: Kiểm Tra
1. Xem tóm tắt tính toán
2. Đảm bảo không có cảnh báo
3. Lưu dự án

### Bước 4: Xác Nhận
1. Vào trang Project Detail
2. Kiểm tra hiển thị tiến độ đầu tư chính xác

## Troubleshooting

### Vấn đề: Tiến độ hiển thị không chính xác
**Nguyên nhân**: 3 giá trị không nhất quán
**Giải pháp**: Sử dụng form tính toán tự động

### Vấn đề: Số tiền còn lại sai
**Nguyên nhân**: available_share không được cập nhật đúng
**Giải pháp**: Kiểm tra logic đầu tư và cập nhật available_share

### Vấn đề: Tính toán bị lỗi
**Nguyên nhân**: Giá trị âm hoặc bằng 0
**Giải pháp**: Đảm bảo tất cả giá trị đều dương

## Kết Luận

Với hệ thống tính toán tự động này:
- ✅ Đảm bảo tính nhất quán dữ liệu
- ✅ Hiển thị chính xác tiến độ đầu tư
- ✅ Dễ dàng quản lý và setup dự án
- ✅ Tránh sai lệch và xung đột dữ liệu
- ✅ UX/UI thân thiện với người dùng 