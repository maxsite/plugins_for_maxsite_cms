$(document).ready(function () {
    var uloginNetwork = $('#ulogin_accounts').find('.ulogin_network');
    uloginNetwork.click(function () {
        var network = $(this).attr('data-ulogin-network');
        var identity = $(this).attr('data-ulogin-identity');
        uloginDeleteAccount(network,identity);
    });
});

function uloginDeleteAccount(network, identity) {
    var query = $.ajax({
        type: 'POST',
        url: '../index.php',
        data: {
            identity: identity,
            network: network
        },
        dataType: 'json',
        error: function (data) {
            alert('Error');
        },
        success: function (data) {
            if (data.answerType == 'error') {
                alert(data.msg);
            }
            if (data.answerType == 'ok') {
                var accounts = $('#ulogin_accounts');
                nw = accounts.find('[data-ulogin-network=' + network + ']');
                if (nw.length > 0) nw.hide();
                alert(data.msg);
            }
        }
    });
    return false;
}