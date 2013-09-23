<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 *
 * Also, keep this configuration as sparse as possible. Do not add anything to
 * this file unless there's no where else for it to reasonably go.
 *
 * When developing a new site, you will want to change your domain to match
 * your new domain.
 *
 * When developing a module, you will want to leave the domain as example and
 * change the region to the name of the module. For instance, for an "Accounts"
 * module, you might call the region "accounts".
 *
 * If you needed a .SLD.TLD such as .co.uk you can simply change the Suffix to .co.uk
 * 
 * This way you can generate the vhost, add a hosts file, and then visit:
 *   accounts.example.com
 *   cornerstone.example.com
 *   data-modeling.example.com
 *   etc.
 *
 * If you're working with multiple developers in an office for example and you
 * have custom DNS available for your company, say for a fictitious site and you want
 * the other devs to be able to see your work, you can set a Prefix as well, which
 * would result in a url as follows:
 *  bob.www.fictitious.com
 *  james.www.fictitious.com
 *
 * It also works well for internal staging and test environments:
 *  staging.www.fictitious.com
 *  test.www.fictitious.com
 * 
 * Generally, I would recommend matching your environment configs to match the "Prefix"
 * 
 * This file can be copied to global.php and edited to setup the global configuration
 * for new applications.
 */
return array (
    'Installation' => array (
        'Vhost' => array (
            'Server' => array (
                'Domain' => 'example',
                'Region' => 'cornerstone.'
            )
        )
    )
);