<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Admin_Change_Order_Status')) :

    /**
     * Order status change
     *
     * An email sent to the vendor when admin change order status.
     *
     * @class 		WC_Email_Admin_Change_Order_Status
     * @version		3.7.2
     * @package		WooCommerce/Classes/Emails
     * @author 		WooThemes
     * @extends 	WC_Email
     */
    class WC_Email_Admin_Change_Order_Status extends WC_Email {

        var $user_login;
        var $user_email;
        var $user_pass;

        /**
         * Constructor
         *
         * @access public
         * @return void
         */
        function __construct() {
            global $WCMp;

            $this->id = 'admin_change_order_status';

            $this->title = __('Order status change by admin', 'dc-woocommerce-multi-vendor');
            $this->description = __('New order emails are sent when a admin changed order status', 'dc-woocommerce-multi-vendor');

            $this->template_base = $WCMp->plugin_path . 'templates/';
            $this->template_html = 'emails/change-order-status-by-admin.php';
            $this->template_plain = 'emails/plain/change-order-status-by-admin.php';

            // Call parent constuctor
            parent::__construct();
        }

        /**
         * trigger function.
         *
         * @access public
         * @return void
         *
         * @param unknown $order_id
         */
        function trigger($order_id, $new_status, $vendor) {
            global $WCMp;

            if (!$this->is_enabled())
                return;
            
            $this->object = $post;
            $this->find[] = '{order_id}';
            $this->order_id = $order_id;
            $this->replace[] = $this->order_id;

            $this->find[] = '{new_status}';
            $this->new_status = $new_status;
            $this->replace[] = $this->new_status;

            // Other settings
            $this->recipient = $vendor->user_data->data->user_email;

            if ( $this->is_enabled() && $this->get_recipient() ) {
                $a = $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
            }
        }

        /**
         * Get email subject.
         *
         * @access  public
         * @return string
         */
        public function get_default_subject() {
            return apply_filters('wcmp_admin_change_order_status_email_subject', __('[{blogname}] Admin has changed order status - #{order_id}', 'dc-woocommerce-multi-vendor'), $this->object);
        }

        /**
         * Get email heading.
         *
         * @access  public
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('wcmp_admin_change_order_status_email_heading', __('Order status changed by admin: #{order_id}', 'dc-woocommerce-multi-vendor'), $this->object);
        }

        /**
         * get_content_html function.
         *
         * @access public
         * @return string
         */
        function get_content_html() {
            ob_start();
            wc_get_template($this->template_html, array(
                'order_id' => $this->order_id,
                'new_status' => $this->new_status,
                'email_heading' => $this->get_heading(),
                'email'         => $this,
                    ), 'dc-product-vendor/', $this->template_base);

            return ob_get_clean();
        }

        /**
         * get_content_plain function.
         *
         * @access public
         * @return string
         */
        function get_content_plain() {
            ob_start();
            wc_get_template($this->template_plain, array(
                'order_id' => $this->order_id,
                'new_status' => $this->new_status,
                'email_heading' => $this->get_heading(),
                'email'         => $this,
                    ), 'dc-product-vendor/', $this->template_base);

            return ob_get_clean();
        }

        /**
         * Initialise Settings Form Fields
         *
         * @access public
         * @return void
         */
        function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'dc-woocommerce-multi-vendor'),
                    'type' => 'checkbox',
                    'label' => __('Enable this email notification.', 'dc-woocommerce-multi-vendor'),
                    'default' => 'yes'
                ),
                'subject' => array(
                    'title' => __('Subject', 'dc-woocommerce-multi-vendor'),
                    'type' => 'text',
                    'description' => sprintf(__('This controls the email subject line. Leave it blank to use the default subject: <code>%s</code>.', 'dc-woocommerce-multi-vendor'), $this->get_default_subject()),
                    'placeholder' => '',
                    'default' => ''
                ),
                'heading' => array(
                    'title' => __('Email Heading', 'dc-woocommerce-multi-vendor'),
                    'type' => 'text',
                    'description' => sprintf(__('This controls the main heading contained within the email notification. Leave it blank to use the default heading: <code>%s</code>.', 'dc-woocommerce-multi-vendor'), $this->get_default_heading()),
                    'placeholder' => '',
                    'default' => ''
                ),
                'email_type' => array(
                    'title' => __('Email Type', 'dc-woocommerce-multi-vendor'),
                    'type' => 'select',
                    'description' => __('Choose which format of email to be sent.', 'dc-woocommerce-multi-vendor'),
                    'default' => 'html',
                    'class' => 'email_type',
                    'options' => array(
                        'plain' => __('Plain Text', 'dc-woocommerce-multi-vendor'),
                        'html' => __('HTML', 'dc-woocommerce-multi-vendor'),
                        'multipart' => __('Multipart', 'dc-woocommerce-multi-vendor'),
                    )
                )
            );
        }

    }

endif;