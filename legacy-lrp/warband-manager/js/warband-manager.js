/**
 * Created by Richard on 11/02/2018.
 */
(function ($) {


    var visualSuccess = function (element, message) {
        if (!element) return;
        element = $(element);
        var row = element.parent().parent();
        row.addClass("alert-success");
        var column = element.parent();
        if (!message || typeof(message) !== "string") message = "Success"
        column.html('<div><span>' + message + '</span>' +
            '<button type="button" class="close close-fix" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button></div>');
    }

    var visualFailure = function (element, message) {
        if (!element) return;
        element = $(element);
        var row = element.parent().parent();
        row.addClass("alert-danger");
        var column = element.parent();
        if (!message || typeof(message) !== "string") message = "An Error Occurred"
        column.html('<div><span>' + message + '</span></div>');
    }

    var buttonFailure = function (element, message) {
        if(!element) return;
        element = $(element);
        var span = element.parent();
        span.addClass("alert alert-danger");
        if(!message || typeof(message) !== "string") message = "Failed - Please refresh";
        span.html(message);
    }

    WarbandApp = {
        //$(document).ready(function(){});
        approveUser(){
            var element = this;
            var userId = $(element).data("id");
            if (!userId) return;
            var wbcode = $(element).data("wbcode");
            if (!wbcode) return;
            var wbid = $(element).data("wbid");
            if (!wbid) return;
            var data = {
                'action': 'set_user_approved',
                'userId': userId,
                'wbcode': wbcode,
                'wbid': wbid
            };
            // We can also pass the url value separately from ajaxurl for front end AJAX implementations
            $.post(ajax_object.ajax_url, data, function (...response) {
                console.log('Got this from the server: ' + response);
                var jsonResponse = JSON.parse(response[0]);
                if (jsonResponse.success) {
                    visualSuccess(element, "Approval Successful");
                } else {
                    visualFailure(element, "Error Approving User - Please reload page to try again");
                }
            }).fail(function () {
                visualFailure(element, "Error Approving User - Please reload page to try again");
            });
        },
        rejectUser(){
            var result = confirm('Are you sure?');
            if (result) {
                var element = this;
                var userId = $(element).data("id");
                if (!userId) return;
                var data = {
                    'action': 'set_user_rejected',
                    'userId': userId
                };
                // We can also pass the url value separately from ajaxurl for front end AJAX implementations
                $.post(ajax_object.ajax_url, data, function (...response) {
                    console.log('Got this from the server: ' + response);
                    var jsonResponse = JSON.parse(response[0]);
                    if (jsonResponse.success) {
                        visualSuccess(element, "User Rejected");
                    } else {
                        visualFailure(element, "Error Approving User - Please reload page to try again");
                    }
                }).fail(function (...response) {
                    console.log('Got this from the server: ' + response);
                    visualFailure(element, "Error Approving User - Please reload page to try again");
                });
            }
        },
        setWarbandMembershipPublic(){
            var result = confirm('Are you sure?');
            if (result) {
                var element = this;
                var warbandId = $(element).data("wbid");
                if (!warbandId) return;

                var warbandName = $(element).data("name");
                if (!warbandName) return;
                var data = {
                    'action': 'set_warband_membership_public',
                    'wbid': warbandId,
                    'wbname': warbandName
                };
                // We can also pass the url value separately from ajaxurl for front end AJAX implementations
                $.post(ajax_object.ajax_url, data, function (...response) {
                    console.log('Got this from the server: ' + response);
                    var jsonResponse = JSON.parse(response[0]);
                    if (jsonResponse.success) {
                        window.location.reload(true);
                    } else {
                        buttonFailure(element, "Error Making Warband Public - Please reload page to try again");
                    }
                }).fail(function (...response) {
                    buttonFailure(element, "Error Making Warband Public - Please reload page to try again");
                });
            }
        },
        setWarbandMembershipPrivate(){
            var result = confirm('Are you sure?');
            if (result) {
                var element = this;
                var warbandId = $(element).data("wbid");
                if (!warbandId) return;

                var warbandName = $(element).data("name");
                if (!warbandName) return;
                var data = {
                    'action': 'set_warband_membership_private',
                    'wbid': warbandId,
                    'wbname': warbandName
                };
                // We can also pass the url value separately from ajaxurl for front end AJAX implementations
                $.post(ajax_object.ajax_url, data, function (...response) {
                    console.log('Got this from the server: ' + response);
                    var jsonResponse = JSON.parse(response[0]);
                    if (jsonResponse.success) {
                        window.location.reload(true);
                    } else {
                        buttonFailure(element, "Error Making Warband Private - Please reload page to try again");
                    }
                }).fail(function (...response) {
                    buttonFailure(element, "Error Making Warband Private - Please reload page to try again");
                });
            }
        }
    }

    $(document).ready(function () {
        $('.wb-user-approve').on("click", WarbandApp.approveUser);
        $('.wb-user-reject').on("click", WarbandApp.rejectUser);
        $('.wb-make-public').on("click", WarbandApp.setWarbandMembershipPublic);
        $('.wb-make-private').on("click", WarbandApp.setWarbandMembershipPrivate);
    })

    return WarbandApp;
})(jQuery);
