
/**
* Theme: Ubold Admin Template
* Author: Coderthemes
* Nestable Component
*/

/**
* Theme: Ubold Admin Template
* Author: Coderthemes
* Nestable Component
*/

!function ($) {
    "use strict";

    var Nestable = function () { };

    Nestable.prototype.updateOutput = function (e) {
        var list = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
        } else {
            output.val('JSON browser support required for this demo.');
        }
    },
    //init
    Nestable.prototype.init = function () {


        // activate Nestable for list 2
        $('#nestable_list_2').nestable({
            group: 1,
            maxDepth: 1
        }).on('change', function () {
            var r = $('#nestable_list_2').nestable('serialize');
            $("#xx").html(JSON.stringify(r));
        });

        // output initial serialised data
        


    },
    //init
    $.Nestable = new Nestable, $.Nestable.Constructor = Nestable
}(window.jQuery),

//initializing 
function ($) {
    "use strict";
    $.Nestable.init()
}(window.jQuery);
