If you're finding by Contains then it'll be like this

    $("input[id*='DiscountType']").each(function (i, el) {
         //It'll be an array of elements
     });
If you're finding by Starts With then it'll be like this

    $("input[id^='DiscountType']").each(function (i, el) {
         //It'll be an array of elements
     });
If you're finding by Ends With then it'll be like this

     $("input[id$='DiscountType']").each(function (i, el) {
         //It'll be an array of elements
     });
If you want to select elements which id is not a given string

    $("input[id!='DiscountType']").each(function (i, el) {
         //It'll be an array of elements
     });
If you want to select elements which id contains a given word, delimited by spaces

     $("input[id~='DiscountType']").each(function (i, el) {
         //It'll be an array of elements
     });
If you want to select elements which id is equal to a given string or starting with that string followed by a hyphen

     $("input[id|='DiscountType']").each(function (i, el) {
         //It'll be an array of elements
     });