(function( $ ) {
    'use strict';

    $(function() {

        var alertTypes = new Array();
        var alertBasic = '{{alert-type}}, {{datetime}}, {{site-url}}, {{site-name}}, {{user-ip}}, {{user-agent}}';
        alertTypes['login-success'] = '{{password}}, {{username}}, {{login-type}}, {{user-roles}}';
        alertTypes['login-failed'] = '{{password}}, {{username}}, {{login-type}}';
        alertTypes['password-reset'] = '{{username}}, {{user-roles}}, {{user-display-name}}, {{user-email}}, {{new-password}}';
        alertTypes['password-changed'] = '{{username}}, {{new-password}}, {{user-roles}}, {{user-display-name}}, {{user-email}}, {{password-strength}}';
        alertTypes['email-changed'] = '{{username}}, {{user-roles}}, {{user-display-name}}, {{user-email}}, {{user-old-email}}';
        alertTypes['user-register-failed'] = '{{username}}, {{registration-errors}}, {{user-email}}';
        alertTypes['user-register-success'] = '{{username}}, {{user-roles}}, {{user-display-name}}, {{user-email}}';
        alertTypes['user-delete'] = '{{username}}, {{user-roles}}, {{user-display-name}}, {{user-email}}';
        alertTypes['post-new'] = '{{post-date}}, {{post-status}}, {{post-content}}, {{post-title}}, {{post-type}}, {{post-id}}, {{post-name}}';
        alertTypes['post-update'] = '{{post-date}}, {{post-status}}, {{post-content}}, {{post-title}}, {{post-type}}, {{post-id}}, {{post-name}}';
        alertTypes['post-delete'] = '{{post-date}}, {{post-status}}, {{post-content}}, {{post-title}}, {{post-type}}, {{post-id}}, {{post-name}}';
        alertTypes['comment-new'] = '{{comment-id}}, {{comment-post-id}}, {{comment-post-permalink}}, {{comment-author}}, {{comment-author-email}}, {{comment-author-url}}, {{comment-content}}, {{comment-date}}';
        alertTypes['comment-update'] = '{{comment-id}}, {{comment-post-id}}, {{comment-post-permalink}}, {{comment-author}}, {{comment-author-email}}, {{comment-author-url}}, {{comment-content}}, {{comment-date}}';
        alertTypes['comment-spam'] = '{{comment-id}}, {{comment-post-id}}, {{comment-post-permalink}}, {{comment-author}}, {{comment-author-email}}, {{comment-author-url}}, {{comment-content}}, {{comment-date}}, {{comment-new_status}}, {{comment-old_status}}';
        alertTypes['comment-delete'] = '{{comment-id}}, {{comment-post-id}}, {{comment-post-permalink}}, {{comment-author}}, {{comment-author-email}}, {{comment-author-url}}, {{comment-content}}, {{comment-date}}, {{comment-new_status}}, {{comment-old_status}}';
        alertTypes['comment-approved'] = '{{comment-id}}, {{comment-post-id}}, {{comment-post-permalink}}, {{comment-author}}, {{comment-author-email}}, {{comment-author-url}}, {{comment-content}}, {{comment-date}}, {{comment-new_status}}, {{comment-old_status}}';
        alertTypes['comment-unapproved'] = '{{comment-id}}, {{comment-post-id}}, {{comment-post-permalink}}, {{comment-author}}, {{comment-author-email}}, {{comment-author-url}}, {{comment-content}}, {{comment-date}}, {{comment-new_status}}, {{comment-old_status}}';
        alertTypes['cf7-email-sent'] = '{{cf7-[your-filed-name]}}';

        function changeAlertAvailableFields( that, selector ) {
            var alertItem = $( that ).parents( '.' + selector ).find( '.availabe-fields' );
            alertItem.text( alertBasic + ', ' + alertTypes[that.value] );
        }

        $( '.alert-action' ).on('change', function(event) {
            changeAlertAvailableFields( this, 'exopite-sof-accordion__item' );
        });

        $( '.exopite-notificator-form' ).find( '.alert-action' ).each(function(index, el) {
            changeAlertAvailableFields( el, 'exopite-sof-accordion__item' );
        });



    });

})( jQuery );
