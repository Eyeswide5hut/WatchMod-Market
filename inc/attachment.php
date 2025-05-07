<?php
/**
 * The template for displaying attachment pages
 *
 * @package WatchModMarket
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('attachment-page'); ?>>
                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

                    <div class="entry-meta">
                        <?php
                        // Show attachment details
                        $metadata = wp_get_attachment_metadata();
                        if ($metadata) {
                            printf(
                                '<span class="attachment-size">%1$s</span>',
                                esc_html(size_format(filesize(get_attached_file($post->ID))))
                            );
                        }

                        // Show upload date
                        printf(
                            '<span class="posted-on">%1$s <a href="%2$s" rel="bookmark">%3$s</a></span>',
                            esc_html__('Uploaded on', 'watchmodmarket'),
                            esc_url(get_permalink()),
                            esc_html(get_the_date())
                        );

                        // Show attachment author
                        printf(
                            '<span class="byline">%1$s <span class="author vcard"><a class="url fn n" href="%2$s">%3$s</a></span></span>',
                            esc_html__('by', 'watchmodmarket'),
                            esc_url(get_author_posts_url(get_the_author_meta('ID'))),
                            esc_html(get_the_author())
                        );
                        ?>
                    </div><!-- .entry-meta -->
                </header><!-- .entry-header -->

                <div class="attachment-navigation">
                    <div class="nav-links">
                        <div class="nav-previous">
                            <?php previous_image_link(false, '<i class="fa fa-chevron-left"></i> ' . __('Previous', 'watchmodmarket')); ?>
                        </div>
                        <div class="nav-next">
                            <?php next_image_link(false, __('Next', 'watchmodmarket') . ' <i class="fa fa-chevron-right"></i>'); ?>
                        </div>
                    </div>
                </div>

                <div class="entry-content">
                    <div class="attachment-wrapper">
                        <?php
                        // If this is an image attachment
                        if (wp_attachment_is_image($post->ID)) :
                            $img_attributes = wp_get_attachment_image_src(get_the_ID(), 'full');
                            $full_size_link = $img_attributes[0];
                            ?>
                            <div class="attachment-image">
                                <figure class="wp-block-image size-large">
                                    <?php
                                    // Get the image with srcset for responsiveness
                                    echo wp_get_attachment_image(
                                        get_the_ID(),
                                        'large',
                                        false,
                                        array(
                                            'class' => 'wp-image-' . get_the_ID(),
                                            'srcset' => wp_get_attachment_image_srcset(get_the_ID(), 'large'),
                                            'sizes' => wp_get_attachment_image_sizes(get_the_ID(), 'large'),
                                            'alt' => get_the_title(),
                                        )
                                    );
                                    ?>
                                    <?php if (wp_get_attachment_caption()) : ?>
                                        <figcaption class="wp-element-caption"><?php echo wp_kses_post(wp_get_attachment_caption()); ?></figcaption>
                                    <?php endif; ?>
                                </figure>

                                <div class="attachment-actions">
                                    <a href="<?php echo esc_url($full_size_link); ?>" class="btn btn-primary" target="_blank">
                                        <?php esc_html_e('View Full Size', 'watchmodmarket'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(wp_get_attachment_url(get_the_ID())); ?>" class="btn btn-secondary" download>
                                        <?php esc_html_e('Download', 'watchmodmarket'); ?>
                                    </a>
                                </div>
                            </div>

                        <?php else : // Not an image ?>
                            <div class="attachment-file">
                                <?php
                                // Determine file type and show appropriate icon
                                $file_type = wp_check_filetype(wp_get_attachment_url());
                                $file_ext = $file_type['ext'];
                                $file_icon = '';

                                // Simple file type icon mapping
                                switch ($file_ext) {
                                    case 'pdf':
                                        $file_icon = 'fa-file-pdf';
                                        break;
                                    case 'doc':
                                    case 'docx':
                                        $file_icon = 'fa-file-word';
                                        break;
                                    case 'xls':
                                    case 'xlsx':
                                        $file_icon = 'fa-file-excel';
                                        break;
                                    case 'ppt':
                                    case 'pptx':
                                        $file_icon = 'fa-file-powerpoint';
                                        break;
                                    case 'zip':
                                    case 'rar':
                                    case '7z':
                                        $file_icon = 'fa-file-archive';
                                        break;
                                    case 'mp3':
                                    case 'wav':
                                    case 'ogg':
                                        $file_icon = 'fa-file-audio';
                                        break;
                                    case 'mp4':
                                    case 'avi':
                                    case 'mov':
                                        $file_icon = 'fa-file-video';
                                        break;
                                    default:
                                        $file_icon = 'fa-file';
                                }
                                ?>

                                <div class="file-preview">
                                    <div class="file-icon">
                                        <i class="fa <?php echo esc_attr($file_icon); ?>"></i>
                                        <div class="file-ext"><?php echo esc_html(strtoupper($file_ext)); ?></div>
                                    </div>
                                </div>

                                <div class="file-info">
                                    <h3><?php the_title(); ?></h3>
                                    <p class="file-meta">
                                        <?php
                                        // Display file type and size
                                        echo esc_html(strtoupper($file_ext)) . ' ' . esc_html__('File', 'watchmodmarket') . ' - ';
                                        echo esc_html(size_format(filesize(get_attached_file($post->ID))));
                                        ?>
                                    </p>
                                </div>

                                <div class="attachment-actions">
                                    <a href="<?php echo esc_url(wp_get_attachment_url(get_the_ID())); ?>" class="btn btn-primary" download>
                                        <?php esc_html_e('Download File', 'watchmodmarket'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php
                    // Check if description exists
                    if (!empty($post->post_content)) :
                    ?>
                        <div class="attachment-description">
                            <h3><?php esc_html_e('Description', 'watchmodmarket'); ?></h3>
                            <?php the_content(); ?>
                        </div>
                    <?php endif; ?>

                    <?php
                    // If we have EXIF data for images, display it
                    if (wp_attachment_is_image($post->ID) && $metadata = wp_get_attachment_metadata()) :
                    ?>
                        <div class="attachment-metadata">
                            <h3><?php esc_html_e('Image Details', 'watchmodmarket'); ?></h3>
                            <table class="metadata-table">
                                <tbody>
                                    <?php if (isset($metadata['width'], $metadata['height'])) : ?>
                                        <tr>
                                            <th><?php esc_html_e('Dimensions', 'watchmodmarket'); ?></th>
                                            <td><?php echo esc_html($metadata['width'] . ' × ' . $metadata['height'] . ' ' . __('pixels', 'watchmodmarket')); ?></td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php if (isset($metadata['image_meta'])) : 
                                        $image_meta = $metadata['image_meta'];
                                    ?>
                                        <?php if (!empty($image_meta['camera'])) : ?>
                                            <tr>
                                                <th><?php esc_html_e('Camera', 'watchmodmarket'); ?></th>
                                                <td><?php echo esc_html($image_meta['camera']); ?></td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if (!empty($image_meta['aperture'])) : ?>
                                            <tr>
                                                <th><?php esc_html_e('Aperture', 'watchmodmarket'); ?></th>
                                                <td>f/<?php echo esc_html($image_meta['aperture']); ?></td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if (!empty($image_meta['focal_length'])) : ?>
                                            <tr>
                                                <th><?php esc_html_e('Focal Length', 'watchmodmarket'); ?></th>
                                                <td><?php echo esc_html($image_meta['focal_length']); ?>mm</td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if (!empty($image_meta['iso'])) : ?>
                                            <tr>
                                                <th><?php esc_html_e('ISO', 'watchmodmarket'); ?></th>
                                                <td><?php echo esc_html($image_meta['iso']); ?></td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if (!empty($image_meta['shutter_speed'])) : 
                                            $shutter_speed = $image_meta['shutter_speed'];
                                            if ($shutter_speed > 0) {
                                                if ((1 / $shutter_speed) > 1) {
                                                    $shutter = '1/' . round(1 / $shutter_speed);
                                                } else {
                                                    $shutter = $shutter_speed;
                                                }
                                            }
                                        ?>
                                            <tr>
                                                <th><?php esc_html_e('Shutter Speed', 'watchmodmarket'); ?></th>
                                                <td><?php echo esc_html($shutter); ?> sec</td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if (!empty($image_meta['created_timestamp'])) : ?>
                                            <tr>
                                                <th><?php esc_html_e('Date Taken', 'watchmodmarket'); ?></th>
                                                <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $image_meta['created_timestamp'])); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div><!-- .entry-content -->

                <div class="attachment-parent">
                    <?php
                    // Check if parent post exists
                    if ($post->post_parent) :
                        $parent_post = get_post($post->post_parent);
                        if ($parent_post) :
                    ?>
                        <div class="parent-link">
                            <h3><?php esc_html_e('Attached to', 'watchmodmarket'); ?></h3>
                            <a href="<?php echo esc_url(get_permalink($parent_post->ID)); ?>" class="parent-post-link">
                                <?php echo esc_html($parent_post->post_title); ?>
                            </a>
                        </div>
                    <?php
                        endif;
                    endif;
                    ?>
                </div>

                <?php if (comments_open() || get_comments_number()) : ?>
                    <div class="comments-section">
                        <?php comments_template(); ?>
                    </div>
                <?php endif; ?>
            </article><!-- #post-<?php the_ID(); ?> -->

        <?php endwhile; // End of the loop. ?>
    </div>
</main><!-- #main -->

<?php
get_sidebar();
get_footer();