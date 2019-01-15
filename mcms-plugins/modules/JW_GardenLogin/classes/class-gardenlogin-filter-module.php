<?php
/**
 * GardenLogin Settings
 *
 * @since 1.0.19
 */
if ( ! class_exists( 'GardenLogin_Filter_API' ) ) :

	class GardenLogin_Filter_API {


		public function __construct() {

			add_filter( 'modules_api_result', array( $this, 'filter_api_result' ), 10, 3 );
			add_filter( 'module_install_action_links', array( $this, 'filter_action_links' ), 10, 2 );
		}

		/**
		 * Override the installation link for pro modules
		 *
		 * @param  $links  Download link.
		 * @param  $module Modules Attributes
		 *
		 * @since 1.0.19
		 */
		public function filter_action_links( $links, $module ) {

			if ( empty( $module['gardenlogin-result'] ) ) {
				return $links;
			}

			if ( ! empty( $module['buy-now'] ) ) {
				// End if().
				array_pop( $links );
				$link = '<a class="%s" target="_blank" data-slug="' . esc_attr( $module['slug'] ) . '" href="%s" aria-label="%s" data-name="' . esc_attr( $module['name'] ) . '">%s</a>';

				if ( ! empty( $links[0] ) && preg_match( '/install-now/', $links[0] ) ) {
					$links[0] = sprintf(
            $link,
            'button',
            esc_url( $module['buy-now'] ),
            esc_attr( sprintf( __( 'Buy %s now', 'gardenlogin' ), $module['name'] ) ),
            esc_html__( 'Buy Now', 'gardenlogin' )
          );
				}

				// $links[] = sprintf(
        //   $link,
        //   'thickbox open-module-details-modal',
        //   esc_url( $module['buy-now'] . '#TB_iframe=true&width=600&height=550' ),
        //   esc_attr( sprintf( __( 'More information about %s', 'gardenlogin' ), $module['name'] ) ),
        //   esc_html__( 'More Details', 'gardenlogin' )
        // );
			}

			return $links;
		}


		/**
		 * Filter Module search result
		 *
		 * @since 1.0.19
		 */
		function filter_api_result( $result, $action, $args ) {

			if ( empty( $args->browse ) ) {
				return $result;
			}

			if ( 'featured' !== $args->browse && 'recommended' !== $args->browse ) {
				return $result;
			}

			if ( ! isset( $result->info['page'] ) || 1 < $result->info['page'] ) {
				return $result;
			}

			$result_slugs = mcms_list_pluck( $result->modules, 'slug' );

			$products = $this->get_products();

			$count = 0;
			$products_to_inject = array();
			foreach ( $products as $key => $product ) {

				$products[ $key ] = $product = $this->build_product_data( $product );

				// if the product is already installed, skip it
				if ( $product['is_activated'] || $product['is_installed'] ) {
				  continue;
				}

				// if the product is already in the results, skip it
				if ( in_array( $product['slug'], $result_slugs ) ) {
					continue;
				}

				$products_to_inject[] = $product;

				$count++;

				// no of products to show
				if ( 3 === $count ) {
				break;
				}
			}

			// prepend the products that we wish to inject
			for ( $i = count( $products_to_inject ) - 1; 0 <= $i; $i-- ) {
				array_unshift( $result->modules, $products_to_inject[ $i ] );
			}
			return $result;
		}



		/**
		 * Set module data structure acording to code.
		 *
		 * @since 1.0.19
		 */
		function build_product_data( $product_data ) {
      $defaults = array(
        'name'                     => null,
        'slug'                     => null,
        'version'                  => null,
        'author'                   => '<a href="https://jiiworks.net/">JIIWorks</a>',
        'author_profile'           => null,
        'requires'                 => '3.9',
        'tested'                   => '4.8',
        'rating'                   => 100,
        'ratings'                  => array(),
        'num_ratings'              => null,
        'support_threads'          => null,
        'support_threads_resolved' => null,
        'active_installs'          => null,
        'downloaded'               => array(),
        'last_updated'             => null,
        'added'                    => null,
        'homepage'                 => '',
        'sections'                 => array(),
        'short_description'        => null,
        'download_link'            => '',
        'screenshots'              => array(),
        'tags'                     => array(),
        'versions'                 => array(),
        'donate_link'              => null,
        'contributors'             => array(),
        'gardenlogin-result'        => true,
        'is_activated'             => false,
        'is_installed'             => false,
        'icons'                    => array(
          'default'                => null,
        ),
      );

			$product = array_merge( $defaults, $product_data );

			if ( ! empty( $product['title'] ) && empty( $product['name'] ) ) {
				  $product['name'] = $product['title'];
			}

			if ( ! empty( $product['description'] ) && empty( $product['short_description'] ) ) {
				  $product['short_description'] = mcms_trim_words( $product['description'], 27 );
			}

			if ( ! empty( $product['image'] ) && empty( $product['icons']['default'] ) ) {
				if ( 0 === strpos( $product['image'], 'http' ) ) {
					$product['icons']['default'] = $product['image'];
				}
			}

			return $product;
		}


		/**
		 * Get product info
		 *
		 * @since 1.0.19
		 */
		function get_products() {
			$products = array(
        'mcms-analytify' => array(
          'title'           => __( 'Analytify', 'gardenlogin' ),
          'slug'            => 'mcms-analytify',
          'link'            => 'https://analytify.io/',
          #'image'           => 'https://ps.w.org/mcms-analytify/assets/icon-128x128.png?rev=1299138',
          'is_activated'    => class_exists( 'MCMS_Analytify' ),
          'is_installed'    => file_exists( MCMS_PLUGIN_DIR . '/mcms-analytify/mcms-analytify.php' ),
          'active_installs' => '10000',
          'num_ratings'     => 75,
          'last_updated'    => '2017-07-06 7:07pm GMT',
          'description'     => __( 'Analytify is reshaping Google Analytics in MandarinCMS. See Social Media, Keywords, Realtime, Country, Mobile and Browsers Statistics under pages and posts.', 'gardenlogin' ),
        ),
        'related-posts-thumbnails' => array(
          'title'           => __( 'Related Posts', 'gardenlogin' ),
          'slug'            => 'related-posts-thumbnails',
          'link'            => 'https://jiiworks.net/mandarincms/modules/related-posts/',
          #'image'           => 'https://ps.w.org/related-posts-thumbnails/assets/icon-128x128.png?rev=1299138',
          'is_activated'    => class_exists( 'RelatedPostsThumbnails' ),
          'is_installed'    => file_exists( MCMS_PLUGIN_DIR . '/related-posts-thumbnails/related-posts-thumbnails.php' ),
          'active_installs' => '30000',
          'num_ratings'     => 28,
          'last_updated'    => '2017-06-14 8:37pm GMT',
          'description'     => __( 'Related Post Thumbnails module is for those who want the showcase of their related posts after the post detail. The module allows customizing thumbnail sizes, display settings, and type of relations. The module is using original MandarinCMS taxonomy. It returns generated HTML, that is essential for page load speed of blogs that use many Javascript widgets.', 'gardenlogin' ),
        ),
        'gardenlogin-pro' => array(
          'title'           => __( 'GardenLogin Pro', 'gardenlogin' ),
          'slug'            => 'gardenlogin-pro',
          'link'            => 'https://jiiworks.net/mandarincms/modules/gardenlogin/',
          #'image'           => 'https://ps.w.org/gardenlogin/assets/icon-128x128.png?rev=1299138',
          'is_activated'    => class_exists( 'GardenLogin_Pro' ),
          'is_installed'    => file_exists( MCMS_PLUGIN_DIR . '/gardenlogin-pro/gardenlogin-pro.php' ),
          'active_installs' => '1000',
          #'buy-now'         => 'https://jiiworks.net/mandarincms/modules/gardenlogin/?utm_source=gardenlogin-lite&utm_medium=featured-filter&utm_campaign=pro-upgrade',
          'num_ratings'     => 51,
          'last_updated'    => '2017-07-06 7:07pm GMT',
          'description'     => __( 'GardenLogin Module by JiiWorks holds a lot of customization fields to change the layout of the login page of MandarinCMS. You can modify the look and feel of login page completely even the login error messages, forgot error messages, registration error messages, forget password hint message and many more.', 'gardenlogin' ),
        ),
			);

			return $products;
		}
	}
endif;
new GardenLogin_Filter_API();