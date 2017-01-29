<?php
date_default_timezone_set('UTC');
include 'parser.inc';
include 'countries.inc';
    
$map_width = 600;
$map_height = 300;

$timezones = timezone_picker_parse_files($map_width, $map_height,
                                         WEBROOT . '/system/modules/install/assets/js/tz_world.txt',
                                         WEBROOT . '/system/modules/install/assets/js/tz_islands.txt');
$offset_halves = array();
for($i=-11; $i<=13; $i++)
    $offset_halves[$i] = array();
    
$countries_array = array();
?>

<div id="timezone-picker">
    <img id="timezone-image" src="<?= WEBROOT ?>/system/modules/install/assets/images/world.jpg"
        width="<?= $map_width ?>px" height="<?= $map_height ?>px" usemap="#timezone-map" />
    <img class="timezone-pin" src="<?= WEBROOT ?>/system/modules/install/assets/images/pin.png" style="padding-top: 4px;" />
    <map name="timezone-map" id="timezone-map">
    <?php
        foreach ($timezones as $timezone_name => $timezone):
            if(fmod($timezone['offset'], 1) != 0 && !in_array($timezone['offset'], $offset_halves[$timezone['offset']]))
                $offset_halves[$timezone['offset']][] = $timezone['offset'];
            //$countries_array[$timezone['country']] = true;
            foreach ($timezone['polys'] as $coords): ?>
            <area data-timezone="<?php print $timezone_name; ?>"
                  data-country="<?php print $timezone['country']; ?>"
                  data-pin="<?php print implode(',', $timezone['pin']); ?>"
                  data-offset="<?php print $timezone['offset']; ?>"
                  shape="poly" coords="<?php print implode(',', $coords); ?>" />
        <?php endforeach; ?>
        <?php foreach ($timezone['rects'] as $coords): ?>
            <area data-timezone="<?php print $timezone_name; ?>"
                  data-country="<?php print $timezone['country']; ?>"
                  data-pin="<?php print implode(',', $timezone['pin']); ?>"
                  data-offset="<?php print $timezone['offset']; ?>"
                  shape="rect" coords="<?php print implode(',', $coords); ?>" />
        <?php endforeach; ?>
    <?php endforeach; ?>
    </map>
</div>

<fieldset>
    <legend>Timezone</legend>
    <?php
        $country = '';
        $timezone = $_SESSION['install']['saved']['timezone'];
        if(in_array($timezone, $timezones))
            $country = $timezones[$timezone]['country'];
    ?>
    <label>Country
        <select id="edit-site-default-country" name="country">
        <option value="">- None -</option>
        <?php foreach($countryList as $country_code => $country_name):
            $selected = strcmp($country, $country_code) == 0 ? " selected" : "";
        ?>
            <option value="<?= $country_code ?>"<?= $selected ?>><?= $country_name ?></option>
        <?php endforeach; ?>
        </select>
    </label>

    <label>Timezone <small><em>eg: <?= $_SESSION['install']['default']['timezone'] ?></em></small>
        <select id="edit-date-default-timezone" name="timezone">
            <option value="">- None -</option>
        <?php
            // not alphabetised.... need usort
            $past_optgroup = '';
            foreach($timezones as $timezone_name => $tz):
                $parts = explode("/", $timezone_name);
                $selected = strcmp($_SESSION['install']['saved']['timezone'], $timezone_name) == 0 ? " selected" : "";
            if(strcmp($past_optgroup, $parts[0]) !== 0) :
                if(!empty($past_optgroup)):
                ?>
                    </optgroup>
                <?php endif;

                $past_optgroup = $parts[0];
            ?>
                <optgroup label="<?= $parts[0] ?>">
            <?php endif;
        ?>
            <option value="<?= $timezone_name ?>"<?= $selected ?>><?= str_replace("_", " ", $timezone_name) ?></option>
        <?php endforeach; ?>
            </optgroup>
        </select>
    </label>

    <!--<pre><?= print_r($offset_halves, true) ?></pre>//-->

    <label>GMT
        <select id="edit-gmt-offset" name="gmt">
            <option value="">- None -</option>
        <?php for($i=-11; $i<=13; $i++) : ?>
            <option value="<?= $i ?>"><?= ($i>0 ? "+" : '') . $i ?></option>
            <?php foreach($offset_halves[$i] as $j): ?>
            <option value="<?= $j ?>"><?= ($j>0 ? "+" : '') . $j ?></option>
        <?php endforeach;
        endfor; ?>
        </select>
    </label>
    <br/>
    <button class="button" type="submit">Next</button>
    <?php echo $w->partial('skip', array('skip' => $step+1), 'install'); ?>
</fieldset>

<script type="text/javascript">
<!--

jQuery(document).ready(function($){
                       
    // $('#img-with-usemap-attr').timezonePicker();
                       
    // Set up the picker to update target timezone and country select lists.
    $('#timezone-image').timezonePicker({
        target: '#edit-date-default-timezone',
        countryTarget: '#edit-site-default-country',
        offsetTarget: '#edit-gmt-offset',
        pin: '.timezone-pin',
        fillColor: 'FFCCCC'
    });

    // Optionally an auto-detect button to trigger JavaScript geolocation.
    $('#timezone-detect').click(function() {
        $('#timezone-image').timezonePicker('detectLocation');
    });
});

//-->
</script>
