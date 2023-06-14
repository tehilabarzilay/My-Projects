
$( document ).ready(function() {


    $.getJSON( "https://jsonplaceholder.typicode.com/users", function( data ) {
        var items = [];
        $.each( data, function( key, val ) {
            items.push( "<li class='users' id='" + val.id + "'>" + val.name + "<span>READ MORE</span></li>" );
        });
        $( "<ul/>", {
            "class": "users-list",
            html: items.join( "" )
        }).appendTo( "#users" );

        $('.users').click(function(){
            getTasks($(this).attr('id'));
        });

        $('.users span').click(function(){
            event.stopPropagation();
            getDetails($(this).parent().attr('id'));
        });

    });



    function getTasks(userid) {
        $.getJSON( "https://jsonplaceholder.typicode.com/todos/?userId="+userid, function( data ) {
            var items = [];
            $.each( data, function( key, val ) {
                items.push( "<li class='taskid'>" + val.title + "</li>" );
            });
            $( "#tasks" ).empty();
            $( "<ul/>", {
                "class": "tasks-list",
                html: items.join( "" )
            }).appendTo( "#tasks" );
        });
    }

    function getDetails(userid) {
        $.getJSON( "https://jsonplaceholder.typicode.com/users/"+userid, function( data ) {
            var items = [];
            $.each( data, function( key, val ) {
                items.push( "<li class='details'>" + key + ":"+val+"</li>" );
            });
            $( "#details" ).empty();
            $( "<ul/>", {
                "class": "details-list",
                html: items.join( "" )
            }).appendTo( "#details" );
        });
    }

});

