let cardCount = 2; // Bắt đầu từ 2 vì đã có 2 flashcard mặc định

function addFlashcard() {
    const flashcardGroup = document.querySelector('.flashcard-group');

    // Tạo một thẻ flashcard mới
    const flashcard = document.createElement('div');
    flashcard.className = 'flashcard';
    flashcard.innerHTML = `
        <span class="card-number">${cardCount + 1}</span> <!-- Số thứ tự của thẻ mới -->
        <input type="text" name="terms[]" placeholder="Thuật ngữ" required>
        <input type="text" name="definitions[]" placeholder="Định nghĩa" required>
        <input type="text" name="examples[]" placeholder="Ví dụ"> <!-- Không bắt buộc -->
        <label class="image-upload">
            <input type="file" name="images[]" accept="image/*"> <!-- Không bắt buộc -->
            <!-- Xóa thẻ span để hiển thị tên file -->
        </label>
        <button type="button" class="delete-btn" onclick="deleteFlashcard(this)">🗑️</button>
    `;

    flashcardGroup.appendChild(flashcard); // Thêm thẻ flashcard mới vào nhóm
    cardCount++; // Tăng số đếm thẻ lên 1

    updateCardNumbers(); // Cập nhật số thứ tự sau khi thêm thẻ mới
}

function deleteFlashcard(button) {
    const flashcard = button.parentElement; // Lấy thẻ flashcard
    flashcard.remove(); // Xóa thẻ flashcard

    // Cập nhật lại số thứ tự cho các flashcard còn lại
    updateCardNumbers();
}

function updateCardNumbers() {
    const flashcards = document.querySelectorAll('.flashcard');

    // Cập nhật số thứ tự cho từng thẻ
    flashcards.forEach((flashcard, index) => {
        flashcard.querySelector('.card-number').textContent = index + 1; // Cập nhật số thứ tự bắt đầu từ 1
    });

    // Cập nhật giá trị cardCount cho các thẻ hiện tại
    cardCount = flashcards.length; // Cập nhật lại số lượng thẻ hiện tại
}
