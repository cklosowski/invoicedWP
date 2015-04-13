<?php
 /*Template Name: New Template
 */
 
get_header(); ?>
<div id="primary">
    <div id="content" role="main">
    <?php
    $mypost = array( 'post_type' => 'invoicedwp', );
    $loop = new WP_Query( $mypost );
    
    while ( have_posts() ) : the_post();

        $invoiceContent = get_post_meta( get_the_id(), '_invoicedwp', true );

    ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <div id="logo">
                    <img src="logo.png">
                </div>
                <h1>INVOICE 3-2-1</h1>
                <div id="company" style="width: 45%; float: left;">
                    <div>Company Name</div>
                    <div>455 Foggy Heights,<br> AZ 85004, US</div>
                    <div>(602) 519-0450</div>
                    <div><a href="mailto:company@example.com">company@example.com</a></div>
                </div>
                <div id="project" style="width: 45%; float: right;">
                    <div><span><?php _e( 'Project:', 'invoicedwp' ); ?></span> <?php echo get_the_title(); ?></div>
                    <div><span><?php _e( 'Client:', 'invoicedwp' ); ?></span> <?php echo $invoiceContent['user_data']['company_name']; ?></div>
                    <div><span><?php _e( 'Name:', 'invoicedwp' ); ?></span> <?php echo $invoiceContent['user_data']['first_name'] . '  ' . $invoiceContent['user_data']['last_name']; ?></div>
                    <div><span><?php _e( 'Address:', 'invoicedwp' ); ?></span> <?php echo $invoiceContent['user_data']['streetaddress']; ?><br /> <?php echo $invoiceContent['user_data']['streetaddress2']; ?></div>
                    <div><span><?php _e( 'Email:', 'invoicedwp' ); ?></span> <a href="<?php echo $invoiceContent['user_data']['user_email']; ?>"><?php echo $invoiceContent['user_data']['user_email']; ?></a></div>
                    <div><span><?php _e( 'Phone:', 'invoicedwp' ); ?></span> <?php echo $invoiceContent['user_data']['phonenumber']; ?></div>
                    <div><span><?php _e( 'DATE:', 'invoicedwp' ); ?></span> August 17, 2015</div>
                    <div><span><?php _e( 'Due Date:', 'invoicedwp' ); ?></span> <?php echo $invoiceContent['user_data']['paymentDueDate']; ?></div>
                </div>
            </header>
 
            <!-- Display movie review contents -->
            <div class="entry-content" style="clear:both;" ><?php the_content(); ?></div>

            <main>
                <table>
                    <thead>
                        <tr>
                            <th class="service"><?php _e( 'Service', 'invoicedwp' ); ?></th>
                            <th><?php _e( 'Price', 'invoicedwp' ); ?></th>
                            <th><?php _e( 'QTY', 'invoicedwp' ); ?></th>
                            <th><?php _e( 'Total', 'invoicedwp' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $invoiceContent['lineItems']['iwp_invoice_name'] as $key => $lineItem ){ ?>
                            <tr>
                                <td class="service">
                                    <?php echo $invoiceContent['lineItems']['iwp_invoice_name'][$key]; ?><br />
                                    <span style="font-size: 0.8em;"><?php echo $invoiceContent['lineItems']['iwp_invoice_description'][$key]; ?></span>
                                </td>
                                <td class="unit"><?php echo $invoiceContent['lineItems']['iwp_invoice_price'][$key]; ?></td>
                                <td class="qty"><?php echo $invoiceContent['lineItems']['iwp_invoice_qty'][$key]; ?></td>
                                <td class="total"><?php echo $invoiceContent['lineItems']['iwp_invoice_total'][$key]; ?></td>
                            </tr>
                        <?php } ?>
                        
                        
                        <tr>
                            <td colspan="3"><?php _e( 'Subtotal', 'invoicedwp' ); ?></td>
                            <td class="total"><?php echo $invoiceContent['invoice_totals']['subtotal']; ?></td>
                        </tr>
                         <tr>
                            <td colspan="3"><?php _e( 'Tax', 'invoicedwp' ); ?></td>
                            <td class="total"><?php echo $invoiceContent['invoice_totals']['tax']; ?></td>
                        </tr>
                        <tr>
                            <td colspan="3"><?php _e( 'Adjustments', 'invoicedwp' ); ?></td>
                            <td class="total"><?php echo $invoiceContent['invoice_totals']['adjustments']; ?></td>
                        </tr>
                        <tr>
                            <td colspan="3"><?php _e( 'Discount', 'invoicedwp' ); ?></td>
                            <td class="total"><?php echo $invoiceContent['invoice_totals']['discount']; ?></td>
                        </tr>
                       
                        <tr>
                            <td colspan="3"><?php _e( 'Payments', 'invoicedwp' ); ?></td>
                            <td class="total"><?php echo $invoiceContent['invoice_totals']['payments']; ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="grand total"><?php _e( 'Total', 'invoicedwp' ); ?></td>
                            <td class="grand total"><?php echo $invoiceContent['invoice_totals']['total']; ?></td>
                        </tr>
                    </tbody>
                </table>
                <div id="notices">
                    <div>NOTICE:</div>
                    <div class="notice"><?php echo $invoiceContent['invoice_notice']; ?></div>
                </div>
            </main>
            <footer>
                Invoice was created on a computer and is valid without the signature and seal.
            </footer>
        </article>
 
    <?php endwhile; ?>
    </div>
</div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>