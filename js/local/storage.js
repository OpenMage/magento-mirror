
var renderStorage = function(jsonValue, expires) {
    if (jsonValue === undefined && expires === undefined) {
        localStorage.removeItem('viewed_products');
    } else {
        localStorage.setItem('viewed_products', JSON.stringify(jsonValue));
        setTimeout(function() {
            localStorage.removeItem('viewed_products');
        }, expires - Date.now());
    }
    var d = new Date();
    d.setTime(d.getTime() - 1000000);
    document.cookie = "viewed_products=; expires=" + d.toUTCString() + "; path=/; domain=" + document.domain;
};

var ajaxGotViewed = function(asyncr, lifetime) {
       jQuery.ajax({
           url: "/viewedproducts/storage/gather",
           type: "POST",
           async: asyncr,
           data: {lifetime: lifetime},
           success: function (response) {
               var parsed = JSON.parse(response);
               renderStorage(parsed.products_info, parsed.expiry);
           },
           error: function () {
               document.cookie = "viewed_products=fail; path=/";
           }
       });
};
