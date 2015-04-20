        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-content">
                <div id="logo">
                    <img src="<?php echo $iwp_options['business_logo']; ?>">
                </div>
                <h1><?php if ( $invoiceContent['isQuote'] == 1 ) { _e( 'Quote', 'iwp-txt' ); } else { _e( 'Invoice', 'iwp-txt' ); } ?></h1>
                <div id="company" style="width: 49%; float: left;">
                    <div><?php echo $iwp_options['business_name']; ?></div>
                    <div><?php echo $iwp_options['business_address1']; ?><br> 
                    <?php if( ! empty( $iwp_options['business_address2'] ) ) { echo $iwp_options['business_address2'] . "<br>"; } ?>
                    <?php echo $iwp_options['business_city']; ?> <?php echo $iwp_options['business_state']; ?>, <?php echo $iwp_options['business_zip_code']; ?><br><?php echo $iwp_options['business_country']; ?></div>
                    <div><?php echo $iwp_options['business_phone_number']; ?></div>
                    <div><a href="mailto:<?php echo $iwp_options['business_email']; ?>"><?php echo $iwp_options['business_email']; ?></a></div>
                </div>
                <div id="project" style="width: 50%; float: right;">
                    <div><span><?php _e( 'Project:', 'invoicedwp' ); ?></span> <?php echo get_the_title(); ?></div>
                    <?php if( ! empty( $invoiceContent['user_data']['company_name'] ) ) { ?><div><span><?php _e( 'Client:', 'invoicedwp' ); ?></span> <?php echo $invoiceContent['user_data']['company_name']; ?></div><?php } ?>
                    <div><span><?php _e( 'Name:', 'invoicedwp' ); ?></span> <?php echo $invoiceContent['user_data']['first_name'] . '  ' . $invoiceContent['user_data']['last_name']; ?></div>
                    <div><span><?php _e( 'Address:', 'invoicedwp' ); ?></span><br /> <?php echo $invoiceContent['user_data']['streetaddress'] . ' '; if( ! empty( $iwp_options['business_address2'] ) ) { echo $invoiceContent['user_data']['streetaddress2'] . ' '; } echo $invoiceContent['user_data']['city'] . ', ' . $invoiceContent['user_data']['state'] . ', ' . $invoiceContent['user_data']['zip']; ?></div>
                    <div><span><?php _e( 'Email:', 'invoicedwp' ); ?></span> <a href="<?php echo $invoiceContent['user_data']['user_email']; ?>"><?php echo $invoiceContent['user_data']['user_email']; ?></a></div>
                    <div><span><?php _e( 'Phone:', 'invoicedwp' ); ?></span> <?php echo $invoiceContent['user_data']['phonenumber']; ?></div>
                    <div><span><?php _e( 'DATE:', 'invoicedwp' ); ?></span> <?php echo the_time('F j, Y'); ?></div>
                    <?php if( $invoiceContent['paymentDueDate'] == 1 ) { ?>
                        <div><strong><span><?php _e( 'Due Date:', 'invoicedwp' ); ?></span> <?php echo $invoiceContent['paymentDueDateText']; ?></strong></div>
                    <?php } ?>
                    <?php if( $invoiceContent['minPayment'] == 1 ) { ?>
                        <div style="font-size: 0.8em;"><span><?php _e( 'Minimum Payment Due:', 'invoicedwp' ); ?></span> <?php echo $iwp_currency . ' ' . iwp_format_amount( $invoiceContent['minPaymentText'] ); ?></div>
                    <?php } ?>

                </div>
            </header>
 

            <div class="entry-content" style="clear:both;" ><?php the_content(); ?></div>

            <main style="margin: 0px 10px;">
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
                                <td class="unit"><?php echo $iwp_currency . ' ' . iwp_format_amount( $invoiceContent['lineItems']['iwp_invoice_price'][$key] ); ?></td>
                                <td class="qty"><?php echo $invoiceContent['lineItems']['iwp_invoice_qty'][$key]; ?></td>
                                <td class="total" style="text-align: right;"><?php echo $iwp_currency . ' ' . iwp_format_amount( $invoiceContent['lineItems']['iwp_invoice_total'][$key] ); ?></td>
                            </tr>
                        <?php } ?>
                        
                        
                        <tr>
                            <td style="text-align: right;" colspan="3"><?php _e( 'Subtotal', 'invoicedwp' ); ?></td>
                            <td class="total" style="text-align: right;"><?php echo $iwp_currency . ' ' . iwp_format_amount( $invoiceContent['invoice_totals']['subtotal'] ); ?></td>
                        </tr>
                        <?php if( $invoiceContent['invoice_totals']['tax'] > 0 ) { ?>
                        <tr>
                            <td style="text-align: right;" colspan="3"><?php _e( 'Tax', 'invoicedwp' ); ?></td>
                            <td class="total" style="text-align: right;"><?php echo $iwp_currency . ' ' . iwp_format_amount( $invoiceContent['invoice_totals']['tax'] ); ?></td>
                        </tr>
                        <?php } ?>
                        <tr class="hidden_total">
                            <td style="text-align: right;" colspan="3"><?php _e( 'Adjustments', 'invoicedwp' ); ?></td>
                            <td class="total" style="text-align: right;"><?php echo $iwp_currency . ' ' . iwp_format_amount( $invoiceContent['invoice_totals']['adjustments'] ); ?></td>
                        </tr>
                        <?php if( $invoiceContent['invoice_totals']['discount'] > 0 ) { ?>
                        <tr>
                            <td style="text-align: right;" colspan="3"><?php _e( 'Discount', 'invoicedwp' ); ?></td>
                            <td class="total" style="text-align: right;"><?php echo $iwp_currency . ' ' . iwp_format_amount( $invoiceContent['invoice_totals']['discount'] ); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if( $invoiceContent['invoice_totals']["payments"] > 0 ) { ?>
                        <tr>
                            <td style="text-align: right;" colspan="3"><?php _e( 'Payments', 'invoicedwp' ); ?></td>
                            <td class="total" style="text-align: right;"><?php echo $iwp_currency . ' ' . iwp_format_amount( $invoiceContent['invoice_totals']['payments'] ); ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td style="text-align: right;" colspan="3" class="grand_total"><?php _e( 'Total', 'invoicedwp' ); ?></td>
                            <td class="grand_total" style="text-align: right;"><?php echo $iwp_currency . ' ' . iwp_format_amount( $invoiceContent['invoice_totals']['total'] ); ?></td>
                        </tr>
                        <?php if( $invoiceContent['invoice_totals']["payments"] > 0 ) { ?>
                        <tr>
                            <td style="text-align: right;" colspan="3"><?php _e( 'Payments', 'invoicedwp' ); ?></td>
                            <td class="total" style="text-align: right;"><?php echo $iwp_currency . ' ' . iwp_format_amount( $invoiceContent['invoice_totals']['payments'] ); ?></td>
                        </tr>
                        <tr>
                            <td style="text-align: right;" colspan="3"><?php _e( 'Remaining Balance', 'invoicedwp' ); ?></td>
                            <td class="total" style="text-align: right;"><strong><?php echo $iwp_currency . ' ' . iwp_format_amount( $invoiceContent['invoice_totals']['total']= $invoiceContent['invoice_totals']['total'] - $invoiceContent['invoice_totals']['payments'] ); ?></strong></td>
                        </tr>
                        <?php } ?>

                    </tbody>
                </table>
                <div id="notices" class="entry-content">
                    <div class="notice"><?php _e( 'Notices', 'invoicedwp' ); ?>: <?php echo $invoiceContent['invoice_notice']; ?></div>
                </div>
            </main>
            <footer class="entry-footer">
                <?php echo $iwp_options['invoice_global_notice']; ?>
            </footer>
        </article>