/***************************************************************************
 * Roundcube "additional_imap" plugin.              
 ***************************************************************************/

function switch_account(a, b) {
    "self" == b ? document.location.href = "./?_task=mail&_action=plugin.additional_imap&_mbox=INBOX&_switch=" + a : parent.location.href = "./?_task=mail&_action=plugin.additional_imap&_mbox=INBOX&_switch=" + a
}

function remotehint_toggle() {
    var a = rcmail.get_cookie("minimalmode");
    parseInt(a) || $("#topline").css("top") && 0 > $("#topline").css("top").replace("px", "") ? ($("select.deco").hide(), $(".remotehint").css("top", "17px")) : ($("select.deco").show(), $(".remotehint").css("top", "1px"))
}
$(document).ready(function() {
    if (document.getElementById("rcmfd_additional_imap.delimiter"))
        if (document.getElementById("rcmfd_additional_imap.delimiter").readOnly) $(document.getElementById("rcmfd_additional_imap.delimiter")).parent().parent().hide();
        else {
            var a = document.getElementById("rcmfd_additional_imap.delimiter").value,
                b = '<select name="_delimiter" id="delimiter"><option value=".">. (' + rcmail.gettext("additional_imap.dot") + ')</option><option value="/">/ (' + rcmail.gettext("additional_imap.slash") +
                ')</option><option value="\\">\\ (' + rcmail.gettext("additional_imap.backslash") + ")</option></select>";
            document.getElementById("rcmfd_additional_imap.delimiter").parentNode.innerHTML = b;
            $("#delimiter").val(a)
        } document.getElementById("rcmfd_additional_imap.imapserver") && document.getElementById("rcmfd_additional_imap.imapserver").readOnly && $(document.getElementById("rcmfd_additional_imap.imapserver")).parent().parent().hide();
    if ("classic" != rcmail.env.skin && null != $("#accounts").html()) {
        if ($(".username").html($("#accounts").html()),
            $("#accounts").html(""), $("#taskbar .minmodetoggle").on("click", function() {
                window.setTimeout("remotehint_toggle();", 200)
            }), $("#topline").mouseover(function() {
                var a = rcmail.get_cookie("minimalmode");
                (parseInt(a) || $("#topline").css("top") && 0 > $("#topline").css("top").replace("px", "")) && $("select.deco").show()
            }), $("select.deco").focusout(function() {
                var a = rcmail.get_cookie("minimalmode");
                (parseInt(a) || $("#topline").css("top") && 0 > $("#topline").css("top").replace("px", "")) && $("select.deco").hide()
            }), $("#topline").mouseleave(function() {
                var a =
                    rcmail.get_cookie("minimalmode");
                (parseInt(a) || $("#topline").css("top") && 0 > $("#topline").css("top").replace("px", "")) && window.setTimeout("$('select.deco').hide();", 15E3)
            }), $("#mainscreen").mouseover(function() {
                var a = rcmail.get_cookie("minimalmode");
                (parseInt(a) || $("#topline").css("top") && 0 > $("#topline").css("top").replace("px", "")) && $("select.deco").hide()
            }), a = rcmail.get_cookie("minimalmode"), parseInt(a) || $("#topline").css("top") && 0 > $("#topline").css("top").replace("px", "")) $("select.deco").hide(),
            $(".remotehint").css("top", "17px")
    } else $("#accounts").show(), $("#showusername").hide()
});