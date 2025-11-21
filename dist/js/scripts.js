function triggerClick() {
    document.querySelector('#profilImg').click();
}

function displayImage(e) {
    if (e.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            document.querySelector('#profiledisplay').setAttribute('src', e.target.result);
        }
        reader.readAsDataURL(e.files[0]);
    }
}

function triggerClick_edit() {
    document.querySelector('#profilImg_edit').click();
}

function displayImage_edit(e) {
    if (e.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            document.querySelector('#profiledisplay_edit').setAttribute('src', e.target.result);
        }
        reader.readAsDataURL(e.files[0]);
    }
}

// ////////////////////////

function triggerClick_pay() {
    document.querySelector('#profilImge_pay').click();
}

function displayImage_pay(e) {
    if (e.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            document.querySelector('#profiledisplay_pay').setAttribute('src', e.target.result);
        }
        reader.readAsDataURL(e.files[0]);
    }
}