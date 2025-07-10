# Enwikuna License Manager Client

Contributors: Johannes F. Gmelin\
Tags: client, licensing, enwikuna license manager\
Requires at least: 5.4.0\
Tested up to: 6.7.2\
Requires PHP: 7.4.0\
Stable tag: 1.0.1\
License: GPLv3\
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Will help customers to manage their licenses provided by the Enwikuna License Manager and receive automatic updates via
releases.

## Description

The idea behind the Enwikuna License Manager Client is to provide a simple and easy-to-use client to you so that you
don't need to implement your own client for the Enwikuna License Manager REST API. It's specially made to manage
themes & plugins inside WordPress by using standard WordPress functions and hooks.

![Enwikuna License Manager Client](https://www.enwikuna.de/wp-content/uploads/enwikuna-license-manager-client.png)

## Features

- Modern and simple dashboard to manage licenses
- Activate and deactivate licenses
- Provide (automatic) updates via releases to the WordPress updates page (normal & signed)
- Display upgrade notices for plugins
- Inform customer about locked licenses
- Notify customer about license expires soon & expiration
- Intuitive caching to reduce requests to the REST API
- Debugging mode to test locally without the need of an external server
- Cron job to check for updates and license expiration in the background
- Dependency checke for invalid client configuration

## Dependencies

Please note that the Enwikuna License Manager Client is a client plugin for the Enwikuna License Manager! Since the
client requires the Enwikuna License Manager REST API to work correctly, you need to have the following dependencies
installed and activated on your WordPress site where you want to sell your licenses:

- [Enwikuna License Manager](https://wordpress.org/plugins/enwikuna-license-manager/)
- [Enwikuna License Manager Pro](https://www.enwikuna.de/en/products/enwikuna-license-manager/)

## Getting Started

To be able to provide your customers the client, there are some steps you need to perform. In general, it would be good
if you fork this repository to make the necessary changes. This way, you can merge the latest update of the client
directly into your fork. If you don't want to do that, you can also download the latest release
asset `enwikuna-license-manager-client.zip` of the client and edit the files inside the extracted folder.

### Minimum Requirements

* PHP 7.4.0 or greater is recommended (Customer side)
* MySQL 5.6.0 or greater is recommended (Customer side)
* WordPress 5.4.0 or greater is recommended (Customer & Seller side)
* Enwikuna License Manager 1.3.4 or greater is recommended (Seller side)
* Enwikuna License Manager Pro 1.2.3 or greater is recommended (Seller side)

### Configure product & license for the client

Before you can connect the client to the Enwikuna License Manager REST API, you need to configure a product and a
license for the client inside your store! This way, you are able to update the client on the websites of your customers!

#### Create a new product for the client

1) Go to your WooCommerce products on your license selling website running Enwikuna License Manager & Enwikuna License
   Manager Pro and create a new product there. Name it `Enwikuna License Manager Client` and set the **visibility** of
   the product to **private**.
2) After that, you need to mark the product as a license as
   described [here](https://www.enwikuna.de/en/docs/enwikuna-license-manager-how-tos-en/enwikuna-license-manager-create-new-license-products/),
   as a virtual and downloadable product.
3) Now you need to go to the license tab and generate a new UUID for the product. Save it for later!
4) Finally, go to the general settings tab of the product and add a new downloadable file. The name of the file should
   be `Enwikuna License Manager Client` and the file path must point to the latest build of the client (ZIP file)
   e.g. `https://www.example.com/wp-content/uploads/plugins/enwikuna-license-manager-client.zip`.
5) Now you can create and publish the product!

#### Create a generic license for the client

1) Go to the license overview page of the Enwikuna License Manager and create a new license there.
2) The license key **must be the following key**: `AAAA-AAAA-AAAA-AAAA`.
3) The type of the license must be a **normal** license.
4) The product must be the product you have created before. Just search for the product
   name `Enwikuna License Manager Client` and select it.
5) Finally, you can create the license!

### Configure the client

After you have created the product and the license for the client, you need to configure the client to connect to the
REST API of the Enwikuna License Manager. For that, open the main plugin PHP file `enwikuna-license-manager-client.php`.

Inside the file, you can find some constants which need to be set to configure the client!

#### ELMC_COMPANY_NAME

This constant represents the name of your company or shop. It will be used at several places inside the client.

```php
define( 'ELMC_COMPANY_NAME', 'Johannes Themes & Plugins' );
```

#### ELMC_REST_API_URL

This constant contains the URL to the REST API of the Enwikuna License Manager on your website. The URL must end with
`/wp-json/elm/v1/`. The version can change after time, so please make sure that you are using the correct version!

```php
define( 'ELMC_REST_API_URL', 'https://your-page.com/wp-json/elm/v1/' );
```

#### ELMC_REST_API_KEY

This constant contains the REST API key of the Enwikuna License Manager. You can create a new key inside the REST API
settings. You can check out
the [documentation](https://www.enwikuna.de/en/docs/enwikuna-license-manager-configuration-en/enwikuna-license-manager-settings/)
about the settings to understand how to create a new key. The key needs to have the following permissions:

- Permission `Read / Write`
- Route `104`, `106`, `107`, `301`, `302`, `303`, `304` (only if you want to use signed releases)

**Attention! Please copy both the key and the secret since we need it at the next step!**

```php
define( 'ELMC_REST_API_KEY', 'ck_2718fbc3e6521d3ccd775ad2bf3cc575f0154c65' );
```

#### ELMC_REST_API_SECRET

This constant contains the REST API secret of the key, created in the previous step.

```php
define( 'ELMC_REST_API_SECRET', 'cs_3165ee55b1922ab4fd62122fc01bc096e99e1b73' );
```

#### ELMC_PRODUCT_UUID

This constant contains the UUID of the product you have created before. It is used to identify the product inside the
REST API. If you don't set the correct UUID, the client will not work correctly e.g. no self-updating!

```php
define( 'ELMC_PRODUCT_UUID', '81CAA9B9-A858-4691-A529-6DF64B399AC9' );
```

#### ELMC_TRUSTED_SIGNING_KEY

This constant is important when WordPress downloads the latest release of a product! At the moment, signed downloads are
optional inside WordPress but this can change in the future. Anyway, we already support them! The public key can be
found inside the REST API settings of the Enwikuna License Manager. There will be a
dropdown called `Product download public key`. Inside this dropdown, you can find the public key which you need to copy
and set it as value of the constant.

```php
define( 'ELMC_TRUSTED_SIGNING_KEY', 'nlR64NYcav2g8kICpDZ7zTH6ZjiPxfCEHqHCRDXlHu8=' );
```

#### ELMC_DEBUG

There is one last constant which is not part of the general plugin configuration. It is used to enable the debugging
mode for the client. If you set the constant to `true`, you are able to test the client locally without the need of a
remote server running Enwikuna License Manager & Enwikuna License Manager Pro. If you don't enable the debugging mode,
WordPress will not allow any request to the local & unsecure REST API.

```php
define( 'ELMC_DEBUG', true );
```

After all configurations are done, you can save the main client file, create a ZIP file of the client and upload it to
the path set
inside the client product!

### Important hooks for the client

We have added multiple WordPress hooks to the client to give you the possibility to use the client in an easy way!

You can directly put the following hooks inside your product (theme or plugin). If you want to modify the client
globally, you should do this directly inside the code of the client but at the end, this will be your decision.

#### elmc_updatable_products

This hook is very important! It needs to be used to register a product inside the client. The `product_id` (UUID) can
be found inside the license settings of a product within WooCommerce. The `args` array can be used to pass additional
information to the client. The `setup_wizard` key for example is used to define a setup wizard page slug. A button will
become visible on the client dashboard inside the products table which redirects to the setup wizard when pressed
e.g. `https://www.example.com/wp-admin/admin.php?page={the_defined_slug_of_the_setup_wizard}`.

```php
add_filter( 'elmc_updatable_products', 'filter_elmc_updatable_products' );
function filter_elmc_updatable_products( array $products ): array {
    // Register a plugin
    $products[ plugin_basename( __FILE__ ) ] = array(
        'product_id' => '00000000-0000-0000-0000-000000000000',
        'args'       => array(
            'setup_wizard' => 'setup-wizard-page',
        ),
    );

    // Register a theme
    $products[ trailingslashit( get_template() ) . 'style.css' ] = array(
        'product_id' => '00000000-0000-0000-0000-000000000000',
        'args'       => array(
            'setup_wizard' => 'setup-wizard-page',
        ),
    );

    return $products;
}
```

#### elmc_{product_slug}_expiration_offset

This hook can be used to modify the expiration offset of a product. The expiration offset is the time in days when the
client should notify the customer that the license for this product will expire soon! The default value is 7 days.
The placeholder `{product_slug}` needs to be replaced with the correct product slug of the product.

```php
add_filter( 'elmc_{product_slug}_expiration_offset', 'filter_elmc_product_slug_expiration_offset', 10, 2 );
function filter_elmc_product_slug_expiration_offset( int $expiration_offset, ELMC_Product_Abstract $product ): int {
    return 2; // The expiration message will be shown 2 days before the expiration date instead of 7
}
```

#### elmc_{product_slug}_updater_plugin_icon

This hook can only be used for plugins! It is used to set an SVG icon for the plugin on the WordPress update page. If
not set, the default WordPress icon for custom plugins will be used. The placeholder `{product_slug}` needs to be
replaced with the correct product slug of the product.

```php
add_filter( 'elmc_{product_slug}_updater_plugin_icon', 'filter_elmc_product_slug_updater_plugin_icon' );
function filter_elmc_product_slug_updater_plugin_icon(): string {
    // File URL to your plugin SVG icon - below an example
    return untrailingslashit( plugins_url( DIRECTORY_SEPARATOR, __FILE__ ) ) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . 'plugin-icon.svg';
}
```

#### elmc_{product_slug}_renewal_url

This hook can be used to modify the renewal URL for the button shown on the client dashboard when a product has expired.
Generally, the renewal URL is generated by using the URL of the product author and the slug of the product. If you want
to modify this URL, you can use this hook. The placeholder `{product_slug}` needs to be replaced with the correct
product slug of the product.

```php
add_filter( 'elmc_{product_slug}_renewal_url', 'filter_elmc_product_slug_renewal_url', 10, 2 );
function filter_elmc_product_slug_renewal_url( string $renewal_url, ELMC_Product_Abstract $product ): string {
    return 'https://www.example.com/renew/' . $product->get_slug();
}
```

### Check the license status inside your product

To hide or disable things inside your plugin if the license is not activated or has expired, you can build some custom
functions which will allow you to do this. The following code snippets can be used to perform all relevant checks. We
would suggest that you put the following code parts inside your main product class but that's on you.

#### Get the product from the client

You can use this function to get the current product from the client. The returned product can be further used for
several checks. You may need to change the `__FILE__` constant to the correct path pointing to your main product file
e.g. main PHP file of a plugin which is located inside the root folder of your plugin.

```php
public function get_product() {
    if ( class_exists( ELMC::class ) ) {
        return ELMC::get_instance()->get_product( plugin_basename( __FILE__ ) );
    }
    
    return false;
}
```

#### Check registration status

This code can be used to check if the license of the product is registered inside the client.

```php
public function is_registered(): bool {
    $product = $this->get_product();
    
    if ( ! empty( $product ) ) {
        return $product->is_registered();
    }
    
    return false;
}
```

#### Check expiration status

This code can be used to check if the license of the product has expired.

```php
public function has_expired(): bool {
    $product = $this->get_product();
    
    if ( ! empty( $product ) ) {
        return $product->has_expired();
    }
    
    return false;
}
```

#### Proper usage of the defined functions

You can now call the defined functions to do some checks.

```php
if ( $this->is_registered() && ! $this->has_expired() ) {
    // Integrate your main classes or load stuff which should be available if the license is registered and has not expired
}
```

### Test if everything works

After following all the above steps, your client should be ready to use! You can now test the client by creating a new
license for the product you want to manage with the client. You can find the client dashboard linked as a sub-page when
hovering the `Dashboard` entry inside the WordPress admin sidebar! It will have the same name as defined inside
the `ELMC_COMPANY_NAME` constant. If you click it, you should see a table containing your product.

If you get an error message, please check the following:

1) Is the REST API URL correct?
2) Is the REST API key correct?
3) Is the REST API key secret correct?
4) Does the REST API key has all necessary permissions and routes enabled?

Paste the license key of the product into the input field for the license key and press the `Register products` button
below the table. If everything works, you should see a new activation inside the Enwikuna License Manager!

You can also try to create a new release for the product and check if the client is able to display and download the new
version! The version inside the release must be higher than your local product version.

If you set an upgrade message, you should see a new message inside the WordPress update page and also on the plugins
page. The changelog can be found inside the update details of a theme or plugin.

## Automatically download & install the client

If you want to install the client on a customer's website from a theme or plugin automatically, you can use the
following code example. It will download the latest build of the client and install it on the website.

```php
// Register the plugin inside the WordPress plugin repository
add_filter( 'plugins_api', 'filter_plugins_api', 10, 3 );
function filter_plugins_api( $result, string $action, object $args ) {
    $download_url = 'https://www.example.com/wp-content/uploads/plugins/enwikuna-license-manager-client.zip';

    if ( 'plugin_information' !== $action || false !== $result || ! isset( $args->slug ) || 'enwikuna-license-manager-client' !== $args->slug ) {
        return $result;
    }

    $result                = new stdClass();
    $result->name          = 'Enwikuna License Manager Client';
    $result->version       = '';
    $result->download_link = esc_url( $download_url );

    return $result;
}

// Installs a plugin
function install_plugin( array $plugin_to_install, bool $network_wide = false ): bool {
    if ( ! empty( $plugin_to_install['repo-slug'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        WP_Filesystem();

        $skin              = new Automatic_Upgrader_Skin();
        $upgrader          = new WP_Upgrader( $skin );
        $installed_plugins = array_reduce( array_keys( get_plugins() ), 'associate_plugin_file', array() );
        $plugin_slug       = $plugin_to_install['repo-slug'];
        $plugin_file       = $plugin_info['file'] ?? ( $plugin_slug . '.php' );
        $installed         = false;
        $activate          = false;

        // See if the plugin is installed already.
        if ( isset( $installed_plugins[ $plugin_file ] ) ) {
            $installed = true;
            $activate  = ! is_plugin_active( $installed_plugins[ $plugin_file ] );
        }

        // Install this thing!
        if ( ! $installed ) {
            // Suppress feedback.
            ob_start();

            try {
                $plugin_information = plugins_api(
                    'plugin_information',
                    array(
                        'slug'   => $plugin_slug,
                        'fields' => array(
                            'short_description' => false,
                            'sections'          => false,
                            'requires'          => false,
                            'rating'            => false,
                            'ratings'           => false,
                            'downloaded'        => false,
                            'last_updated'      => false,
                            'added'             => false,
                            'tags'              => false,
                            'homepage'          => false,
                            'donate_link'       => false,
                            'author_profile'    => false,
                            'author'            => false,
                        ),
                    )
                );

                if ( is_wp_error( $plugin_information ) ) {
                    throw new Exception( $plugin_information->get_error_message() );
                }

                $package  = $plugin_information->download_link;
                $download = $upgrader->download_package( $package );

                if ( is_wp_error( $download ) ) {
                    throw new Exception( $download->get_error_message() );
                }

                $working_dir = $upgrader->unpack_package( $download, true );

                if ( is_wp_error( $working_dir ) ) {
                    throw new Exception( $working_dir->get_error_message() );
                }

                $result = $upgrader->install_package(
                    array(
                        'source'                      => $working_dir,
                        'destination'                 => WP_PLUGIN_DIR,
                        'clear_destination'           => false,
                        'abort_if_destination_exists' => false,
                        'clear_working'               => true,
                        'hook_extra'                  => array(
                            'type'   => 'plugin',
                            'action' => 'install',
                        ),
                    )
                );

                if ( is_wp_error( $result ) ) {
                    throw new Exception( $result->get_error_message() );
                }

                $activate = true;
            } catch ( Exception $e ) {
                return false;
            }

            // Discard feedback.
            ob_end_clean();
        }

        wp_clean_plugins_cache();

        // Activate this thing.
        if ( $activate ) {
            try {
                if ( $network_wide && ! is_multisite() ) {
                    $network_wide = false;
                }

                $result = activate_plugin( $installed ? $installed_plugins[ $plugin_file ] : $plugin_slug . DIRECTORY_SEPARATOR . $plugin_file, '', $network_wide );

                if ( is_wp_error( $result ) ) {
                    throw new Exception( $result->get_error_message() );
                }

                return true;
            } catch ( Exception $e ) {
                return false;
            }
        }
    }

    return false;
}

function is_plugin_installed( string $plugin_slug ): bool {
    if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    $installed_plugins = get_plugins();
    $plugin_slug       = check_plugin_slug( $plugin_slug );
    
    return array_key_exists( $plugin_slug, $installed_plugins ) || in_array( $plugin_slug, $installed_plugins, true );
}

function is_plugin_active( string $plugin_slug, bool $multisite_check = false ): bool {
    $plugin_slug = check_plugin_slug( $plugin_slug );

    if ( ( ! is_multisite() || ! $multisite_check ) && is_plugin_active( $plugin_slug ) ) {
        return true;
    }

    if ( $multisite_check && is_multisite() && is_plugin_active_for_network( $plugin_slug ) ) {
        return true;
    }

    return false;
}

function check_plugin_slug( string $plugin_slug ): string {
    if ( false === strpos( $plugin_slug, '.php' ) ) {
        $plugin_slug = trailingslashit( $plugin_slug ) . $plugin_slug . '.php';
    }
    
    return $plugin_slug;
}

function associate_plugin_file( array $plugins, string $key ): array {
    $path                 = explode( DIRECTORY_SEPARATOR, $key );
    $filename             = end( $path );
    $plugins[ $filename ] = $key;
    
    return $plugins;
}
```

After providing all the above methods to your theme or plugin, you can finally call the defined functions to install the
client automatically!

### Install the client from a theme during theme activation

```php
add_action( 'after_switch_theme', 'after_switch_theme_action' );
function after_switch_theme_action() {
    if ( ! is_plugin_installed( 'enwikuna-license-manager-client' ) && ! is_plugin_active( 'enwikuna-license-manager-client' ) ) {
        install_plugin(
            array(
                'name'      => 'Enwikuna License Manager Client',
                'repo-slug' => 'enwikuna-license-manager-client',
            ),
            true
        );
    }
}
```

### Install the client from a plugin during plugin installation

```php
register_activation_hook( __FILE__, 'install' );
function install() {
    if ( ! is_plugin_installed( 'enwikuna-license-manager-client' ) && ! is_plugin_active( 'enwikuna-license-manager-client' ) ) {
        install_plugin(
            array(
                'name'      => 'Enwikuna License Manager Client',
                'repo-slug' => 'enwikuna-license-manager-client',
            ),
            true
        );
    }
}
```

## Support

Please note that we don't offer support for any custom code! If you need custom coding, please contact us via our chat
on our [website](https://www.enwikuna.de/en/)! If you have any questions about the client, please create a new issue
inside the GitHub repository!
