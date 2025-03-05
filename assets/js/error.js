// 汎用のバリデーション関数
function validateFields(fields) {
  let hasError = false;
  fields.forEach(function (field) {
    const inputEl = document.getElementById(field.inputId);
    const errorEl = document.getElementById(field.errorId);
    if (errorEl) {
      errorEl.innerHTML = "";
    }
    if (!inputEl || !inputEl.value.trim()) {
      if (errorEl) {
        errorEl.innerHTML = "⚠️ 必須です";
      }
      // 最初のエラー項目にフォーカス（存在する場合）
      if (!hasError && inputEl) {
        inputEl.focus();
      }
      hasError = true;
    }
  });
  return !hasError;
}

// レンタル追加フォーム用バリデーション
function validateRentalForm() {
  return validateFields([
    { inputId: 'addRentalTitle', errorId: 'rentalTitleErrorMessage' },
    { inputId: 'addRentalManager', errorId: 'rentalManagerErrorMessage' },
    { inputId: 'addRentalStart', errorId: 'rentalStartErrorMessage' },
    { inputId: 'addRentalEnd', errorId: 'rentalEndErrorMessage' }
  ]);
}

// 編集イベントフォーム用バリデーション
function validateEditEventForm() {
  return validateFields([
    { inputId: 'editEventTitle', errorId: 'editEventTitleErrorMessage' },
    { inputId: 'editEventManager', errorId: 'editEventManagerErrorMessage' },
    { inputId: 'editEventStart', errorId: 'editEventStartErrorMessage' },
    { inputId: 'editEventEnd', errorId: 'editEventEndErrorMessage' }
  ]);
}