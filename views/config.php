<div id="offline-pre-plugin-container">
    <div class="offline-pre-masthead">
        <div class="offline-pre-masthead__inside-container">
            <div class="offline-pre-masthead__logo-container">
                <img class="offline-pre-masthead__logo"
                     src="<?php echo esc_url( plugins_url( '../_inc/img/logo.png', __FILE__ ) ); ?>"
                     alt="Akismet"/>
            </div>
        </div>
    </div>
    <div class="offline-pre-lower">
        <div class="offline-pre-card">
            <div class="offline-pre-section-header">
                <div class="offline-pre-section-header__label">
                    <span><?php esc_html_e( 'Settings', 'offline-precache' ); ?></span>
                </div>
            </div>
            <div class="inside">
                <form action="<?php echo esc_url( OfflinePrecacheAdmin::get_page_url() ); ?>" method="POST">
                    <table cellspacing="0" class="offline-pre-settings">
                        <tbody>
                        <tr>
                            <th class="offline-pre-enable" width="20%" align="left"
                                scope="row"><?php esc_html_e( 'Enable Caching', 'offline-precache' ); ?></th>
                            <td width="5%"/>
                            <td align="left">
                                <fieldset>
                                            <span class="enable-field switch">
                                                <input id="enabled" name="offline_precache_enabled"
                                                       type="checkbox"  <?php echo esc_attr( get_option( 'offline_precache_enabled' ) ) ? "checked" : ""; ?> class="toggle">
                                                <label for="enabled"></label>
                                            </span>
                                    <span class="offline-pre-note"><?php esc_html_e( 'Register a Service Worker on the store to enable caching assets for faster page load times and viewing previously visited pages while offline.', 'offline-precache' ); ?></span>

                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th class="offline-pre-enable-ga" width="20%" align="left"
                                scope="row"><?php esc_html_e( 'Enable Offline Google Analytics', 'offline-precache' ); ?></th>
                            <td width="5%"/>
                            <td align="left">
                                <fieldset>
                                            <span class="enable-field switch">
                                            <input id="enabled_ga" name="offline_precache_enabled_ga"
                                                   type="checkbox"  <?php echo esc_attr( get_option( 'offline_precache_enabled_ga' ) ) ? "checked" : ""; ?> class="toggle">
                                            <label for="enabled_ga"></label>
                                        </span>
                                    <span class="offline-pre-note"><?php esc_html_e( 'If enabled, caches any Google Analytics tracking events while the visitor is browsing offline and submits them when internet connection is restored.', 'offline-precache' ); ?></span>

                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th class="offline-pre-offline_page" width="20%" align="left"
                                scope="row"><?php esc_html_e( 'Offline Notification Page', 'offline-precache' ); ?></th>
                            <td width="5%"/>
                            <td align="left">
                                <fieldset>
                                            <span class="offline_page">
                                                <?php wp_dropdown_pages( array(
	                                                'name'     => 'offline_precache_page_id',
	                                                'selected' => esc_attr( get_option( 'offline_precache_page_id' ) )
                                                ) ); ?>
                                            </span>
                                    <span class="offline-pre-note"><?php esc_html_e( 'This CMS page will be displayed to visitors when they are browsing offline and have reached a page they haven\'t visited before or tried an action which is not available offline (such as adding to cart).', 'offline-precache' ); ?></span>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th class="offline-pre-cache-strategies" width="20%" align="left"
                                scope="row"><?php esc_html_e( 'Custom Cache Strategies', 'offline-precache' ); ?></th>
                            <td width="5%"/>
                            <td align="left">
                                <fieldset>
                                    <table cellspacing="0" class="cache-strategies-table" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="50%" align="left" scope="col"><?php esc_html_e( 'URL Path', 'offline-precache' ); ?></th>
                                                <th width="25%" align="left" scope="col"><?php esc_html_e( 'Strategy', 'offline-precache' ); ?></th>
                                                <th width="25%" align="center" scope="col"><?php esc_html_e( 'Action', 'offline-precache' ); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php for ($i = 0; $i < count($custom_strategies); $i++):?>
                                            <tr>
                                                <td>
                                                    <span class="url-path"><input  name="custom_strategies[<?php echo $i;?>][path]" type="text" size="15" value="<?php echo esc_attr( $custom_strategies[$i]['path'] ); ?>" class="regular-text code"></span>
                                                </td>
                                                <td>
                                                    <span class="cache-strategy">
                                                        <select name="custom_strategies[<?php echo $i;?>][strategy]">
                                                            <option class="level-0" <?php if(esc_attr($custom_strategies[$i]['strategy']) == "cacheFirst" ) echo "selected"; ?> value="cacheFirst"><?php esc_html_e( 'Cache First', 'offline-precache' ); ?></option>
                                                            <option class="level-0" <?php if(esc_attr($custom_strategies[$i]['strategy']) == "networkFirst" ) echo "selected"; ?> value="networkFirst"><?php esc_html_e( 'Network First', 'offline-precache' ); ?></option>
                                                            <option class="level-0" <?php if(esc_attr($custom_strategies[$i]['strategy']) == "networkOnly" ) echo "selected"; ?> value="networkOnly"><?php esc_html_e( 'Network Only', 'offline-precache' ); ?></option>
                                                        </select>
                                                    </span>
                                                </td>
                                                <td align="center">
                                                    <span class="remove-icon">
                                                        <button class="remove_custom_strategy offline-pre-button"><i class="dashicons dashicons-trash"></i> </button>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endfor;?>
                                            <tr>
                                                <td colspan="3">
                                                    <button id="add_new_custom_strategy" class="offline-pre-button offline-pre-could-be-primary"><?php esc_attr_e( 'Add', 'offline-precache' ); ?></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </fieldset>
                                <span class="offline-pre-note"><?php esc_html_e( 'Specify custom caching strategies used for specific pages. If the path ends with "*", the selected strategy will be applied to all pages starting with the entered path.', 'offline-precache' ); ?></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="offline-pre-card-actions">
						<?php wp_nonce_field( OfflinePrecacheAdmin::NONCE ) ?>
                        <div id="publishing-action">
                            <input type="hidden" name="action" value="save_precache_options">
                            <input type="submit" name="submit" id="submit"
                                   class="offline-pre-button offline-pre-could-be-primary"
                                   value="<?php esc_attr_e( 'Save Changes', 'offline-precache' ); ?>">
                        </div>
                        <div class="clear"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
