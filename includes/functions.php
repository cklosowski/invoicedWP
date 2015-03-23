<?php
/**
 * Helper Functions
 *
 * @package     InvoicedWP
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function invoicedwp_translation_mangler($translation, $text, $domain) {
        global $post;
        
    if ($post->post_type == 'invoicedwp') {
 
        $translations = &get_translations_for_domain( $domain);
        if ( $text == 'Scheduled for: <b>%1$s</b>') {
            return $translations->translate( 'Send On: <b>%1$s</b>' );
        }
        if ( $text == 'Published on: <b>%1$s</b>') {
            return $translations->translate( 'Sent On: <b>%1$s</b>' );
        }
        if ( $text == 'Publish <b>immediately</b>') {
            return $translations->translate( 'Send <b>immediately</b>' );
        }
        if ( $text == 'Schedule') {
            return $translations->translate( 'Schedule send' );
        }
        if ( $text == 'Publish') {
            return $translations->translate( 'Send Invoice' );
        }
        if ( $text == 'Update') {
            return $translations->translate( 'Update and Send' );
        }
    }
 
    return $translation;
}
 
add_filter('gettext', 'invoicedwp_translation_mangler', 10, 4);