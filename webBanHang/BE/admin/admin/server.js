function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function showToast(text, type = 404) {
    $.toast({
        heading: type == 200 ? 'Success <hr>' : 'Error <hr>',
        text: text,
        icon: type == 200 ? 'success' : 'error',
        showHideTransition: 'plain',
        position: 'top-left',
    })
}
function isEmail(txt) {
    return txt.length > 5 && txt.indexOf('@') > 0;
}

class LoginAPI {
    constructor() {
        this.Init();
        this.mesUserErr = { "vi": "Tên tài khoản quá ngắn hoặc đang để trống!" }
        this.mesPassErr = { "vi": "Mật khẩu quá ngắn hoặc đang để trống!" }
        this.mesRePassErr = { "vi": "Mật khẩu nhập vào không khớp!" }
        this.mesEmailErr = { "vi": "Email không hợp lệ!" }
        this.mesPhoneErr = { "vi": "Chưa nhập số phone, hoặc phone không đúng!" }
        this.mesAjaxErr = { "vi": "lỗi ! hãy thử lại sau!" }
        this.list_char = []
        this.account = []
        this.is_enable = true
    }
    Init() {
        this.eVent();
        this.checklogin();
    }
    checklogin() {
        let _this = this
        let info = getCookie('info')
        console.log(info, atob(info))
        let data = {
            "info": info
        }
        $.ajax({
            url: "http://localhost/admin_panel/checklogin.php",
            type: "POST",
            data: data,
            contentType: "application/json",
            data: JSON.stringify(data),
            success: function (response) {
                if (response.success) {
                    let username = response.user.username;
                    $('.say-hello').text(`${username}`);
                    $('#btn_get_info').click()
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }
    eVent() {
        let _this = this;
        $('#btn_logout').click(function () {
            _this.setCookie("info", null, 0)
            showToast("Đăng xuất thành công", 200)
            setTimeout(function() {
                window.location.href= "/admin_panel/index.html"
            }, 3000);
        })
        $('#btn_login').click(function (e) {
            console.log("Button clicked!");
            e.preventDefault();
            if (!_this.is_enable) {
                showToast("Chức năng chưa mở! Hãy quay lại sau!")
                return
            }
            let zone = "vi"
            let username = $('#username').val()
            let password = $('#password').val()
            if (username.length < 6) {
                showToast(_this.mesUserErr[zone])
            } else if (password.length < 6) {
                showToast(_this.mesPassErr[zone])
            } else {
                let data = {
                    "username": username,
                    "password": password,
                    "zone": zone
                }
                _this.Ajax("/admin_panel/login.php", data, zone);
            }

        });
    }
    setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
    Ajax(url, data, zone) {
        let _this = this
        let link = location.origin + url
        $.ajax({
            url: link,
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(data),
            success: function (response) {
                if (response.success) {
                    showToast(response.message, 200)
                    if (url == "/admin_panel/login.php") {
                        let info = btoa(JSON.stringify(response.data))
                        _this.setCookie("info", info, 1)
                        setTimeout(function() {
                            window.location.href = '/admin_panel/home.php';
                        }, 3000);
                    }//setTimeout(() => {
                    //    window.location.href='/admin_panel/index.php'
                    //  }, 3000);
                } else {
                    showToast(response.message)
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // console.log(textStatus, errorThrown);
                showToast(_this.mesAjaxErr[zone])
            }
        });
    }
}
$(() => {
    new LoginAPI();
});