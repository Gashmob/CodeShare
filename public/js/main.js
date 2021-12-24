let shared = false;

$('.share').on('click', function () {
    const title = $('.title')[0].value
    const code = $('.code_area')[0].value

    if (code !== '' && !shared) {
        $('*').css('cursor', 'wait')

        $.post(postUrl, {
            title: title,
            code: code
        }, function (data) {
            if (data['result']) {
                shared = true;
                $('.share')[0].innerHTML = '<i class="fas fa-check"></i> Shared'

                const uid = data['uid'];
                navigator.clipboard.writeText(linkUrl + '' + uid);
            } else {
                $('.share')[0].innerHTML = '<i class="fas fa-times"></i> Error'
            }
            $('*').css('cursor', 'auto')
        })
    }
})