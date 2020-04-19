<?php include 'partials/header.php'; ?>

<div class="littlemonkey--holder">
    <form id="lm_settings_form" method="POST">
        <!-- API KEY -->
        <div class="littlemonkey--line">
            <div class="littlemonkey--input">
                <label>API KEY</label>
                <input type="text" id="lm_api_key" name="lm_api_key" placeholder="Enter your API KEY" value="<?php echo get_option('lm_api_key'); ?>"/>

                <div class="littlemonkey--help">
                    Enter your API KEY, if you do not have one yet, <a href="#"><b>click here</b></a> to get one now for
                    free.
                </div>
            </div>
        </div>

        <!-- COMPRESSION LEVEL -->
        <div class="littlemonkey--line">
            <div class="littlemonkey--input">
                <label>Compression level of images</label>
                <select id="lm_compression_level" name="lm_compression_level">
                    <option value="3" <?php if(get_option('lm_compression_level', 2) == 3) { echo 'selected'; } ?>>High</option>
                    <option value="2" <?php if(get_option('lm_compression_level', 2) == 2) { echo 'selected'; } ?>>Medium</option>
                    <option value="1" <?php if(get_option('lm_compression_level', 2) == 1) { echo 'selected'; } ?>>Low</option>
                </select>

                <div class="littlemonkey--help">
                    <b>High: </b> higher compression, but can result in a small loss of image quality<br />
                    <b>Medium: </b> medium compression without loss image quality<br />
                    <b>Low: </b> only optimize image, without any loss of quality
                </div>
            </div>
        </div>

        <!-- Process new images automatically -->
        <div class="littlemonkey--line">
            <div class="littlemonkey--input">
                <label>Optimize new images automatically?</label>
                <select name="lm_process_uploads" id="lm_process_uploads">
                    <option value="0" <?php if(get_option('lm_process_uploads', 1) == 0) { echo 'selected'; } ?>>No</option>
                    <option value="1" <?php if(get_option('lm_process_uploads', 1) == 1) { echo 'selected'; } ?>>Yes</option>
                </select>

                <div class="littlemonkey--help">
                    Every new upload will be automatically optimized.
                </div>
            </div>
        </div>

        <!-- Image backup -->
        <div class="littlemonkey--line">
            <div class="littlemonkey--input">
                <label>Backup original images?</label>
                <select name="lm_backup" id="lm_backup">
                    <option value="0" <?php if(get_option('lm_backup', 1) == 0) { echo 'selected'; } ?>>No</option>
                    <option value="1" <?php if(get_option('lm_backup', 1) == 1) { echo 'selected'; } ?>>Yes</option>
                </select>

                <div class="littlemonkey--help">
                    Select if you want to keep a copy of all images optimized.
                </div>
            </div>
        </div>

        <!-- Remove EXIF informations -->
        <div class="littlemonkey--line">
            <div class="littlemonkey--input">
                <label>Remove EXIF informations?</label>
                <select name="lm_remove_exif" id="lm_remove_exif">
                    <option value="0" <?php if(get_option('lm_remove_exif', 1) == 0) { echo 'selected'; } ?>>No</option>
                    <option value="1" <?php if(get_option('lm_remove_exif', 1) == 1) { echo 'selected'; } ?>>Yes</option>
                </select>

                <div class="littlemonkey--help">
                    EXIF are information that are automatically embedded in images in creation.
                    If you want to keep remove this data, select <b>yes (recommended)</b>
                </div>
            </div>
        </div>

        <!-- Resize large images -->
        <div class="littlemonkey--line">
            <div class="littlemonkey--input">
                <label>Resize large images</label>

                <select name="lm_resize_large_images" id="lm_resize_large_images">
                    <option value="0" <?php if(get_option('lm_resize_large_images', 0) == 0) { echo 'selected'; } ?>>No</option>
                    <option value="1" <?php if(get_option('lm_resize_large_images', 0) == 1) { echo 'selected'; } ?>>Yes</option>
                </select>

                <div class="littlemonkey--input lm--width-height <?php if(get_option('lm_resize_large_images') == 1) { echo 'lm--force-show'; } ?>">
                    <input type="tel" name="lm_resize_height" placeholder="Max width in pixels" value="<?php echo get_option('lm_resize_height'); ?>" />
                    <input type="tel" name="lm_resize_width" placeholder="Max height in pixels" value="<?php echo get_option('lm_resize_width'); ?>" />
                </div>

                <div class="littlemonkey--help">
                    If resize large images is selected, define an <b>max width</b> and <b>max height</b>.
                    The aspect ratio will be maintained.
                </div>
            </div>
        </div>


        <!-- Thumbnails to resize -->
        <div class="littlemonkey--line">
            <div class="littlemonkey--input">
                <label>Select image sizes to optimize:</label>

                <div class="littlemonkey--size-selector">
                    <?php foreach($thumbnail_sizes as $s) : ?>
                    <div class="littlemonkey--size-selector-line">
                        <input type="checkbox" name="lm_optimize_sizes[<?php echo $s['name'];?>]" id="lm_<?php echo $s['name'];?>"
                        <?php if(!$s['is_custom'] && empty(get_option('lm_optimize_sizes'))) { echo 'checked'; } ?>
                        <?php if(!empty(get_option('lm_optimize_sizes'))) {
                            $sizes = json_decode(get_option('lm_optimize_sizes'));

                            if(in_array($s['name'], $sizes))
                                echo 'checked';
                        }
                        ?>
                        />

                        <label for="lm_<?php echo $s['name'];?>">
                            <?php echo $s['name'];?> ( <?php echo $s['width'];?> x <?php echo $s['height'];?> )
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="littlemonkey--line">
            <div class="littlemonkey--input">
                <button type="submit" class="littlemonkey--btn-default">Save settings</button>
            </div>
        </div>

        <!-- Bulk optimization -->
        <div class="littlemonkey--line <?php if(empty(get_option('lm_api_key'))) { echo 'lm-hide'; } ?>">
            <div class="littlemonkey--input">
                <button type="button" class="littlemonkey--btn-primary">OPTIMIZE IMAGES NOW!</button>
            </div>
        </div>
    </form>
</div>

