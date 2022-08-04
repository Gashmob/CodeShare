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
                const link = linkUrl + '' + uid

                navigator.permissions.query({name: "clipboard-write"}).then(result => {
                    if (result.state === "granted" || result.state === "prompt") {
                        navigator.clipboard.writeText(link);
                        window.open(link, '_blank');
                    }
                });
            } else {
                $('.share')[0].innerHTML = '<i class="fas fa-times"></i> Error'
            }
            $('*').css('cursor', 'auto')
        })
    }
})