// Preloader
$(window).on("load", function() {
    // makes sure the whole site is loaded
    $("#status").fadeOut(); // will first fade out the loading animation
    $("#preloader").delay(750).fadeOut("slow"); // will fade out the white DIV that covers the website.
    $("body").delay(500).css({
        overflow: "visible"
    });
});

// parse a date in yyyy-mm-dd format
function parseDate(input, format) {
    format = format || 'yyyy-mm-dd'; // default format
    var parts = input.match(/(\d+)/g),
        i = 0,
        fmt = {};
    // extract date-part indexes from the format
    format.replace(/(yyyy|dd|mm)/g, function(part) {
        fmt[part] = i++;
    });

    return new Date(parts[fmt['yyyy']], parts[fmt['mm']] - 1, parts[fmt['dd']]);
}

// parse an ISO date format with timezone
function parseISODate(s) {
    var b = s.split(/\D+/);
    return new Date(Date.UTC(b[0], --b[1], b[2], b[3], b[4], b[5], b[6]));
}

// Display Last Updated
var php_date = document.getElementById("phpdatetime").innerHTML;
var localDate = new Date(parseISODate(php_date));
document.getElementById("datetime").innerHTML = localDate.toString();

// Hide global section if no data
var checker = $("#world-cases").text();
if (checker < 1) {
    $(".global-cases").hide();
} else {
    $(".global-cases").show();
}

// Show password
function showPassword() {
    var x = document.getElementById("password");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}