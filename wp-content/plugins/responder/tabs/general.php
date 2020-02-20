<?php 

$versionResponder = HelperResponder::getResponderPluginVersion();

?>
<div style="min-height: 200px;">
<h1>סטטוס תקשורת</h1>

            <table cellspacing="1" cellpadding="2"><tbody>
                <img style="background: white;padding: 20px;border: 2px solid #d5d5ff;float: left;box-shadow: 0 0 2px 2px #f1f1f1;margin-top: -10px;width:180px;" src="<?php echo RAV_MESSER_PLUGIN_URL.'/img/logo.png'; ?>">
            <tr><td>מצב תקשרות: </td>
                <td><?php if( $this ->status == 'on' ){ ?><span class="connected" >מחובר</span><?php } else { ?><span class="not-connected" >לא מחובר</span><?php } ?></td>
            </tr><tr><td>גרסת התוסף:</td>
                <td><?php echo $versionResponder?></td>
            </tr>
            <tr><td>גרסת PHP: </td>
                <td><?php echo phpversion(); ?>
                <?php
                if (version_compare('5.2', phpversion()) > 0) {
                    echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                    _e('(WARNING: This plugin may not work properly with versions earlier than PHP 5.2)', 'responder');
                    echo '</span>';
                }
                ?>
                </td>
            </tr>
            <tr><td>גרסת מסד נתונים: </td>
                <td><?php echo $this->getMySqlVersion() ?>
                    <?php
                    echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                    if (version_compare('5.0', $this->getMySqlVersion()) > 0) {
                        _e('(WARNING: This plugin may not work properly with versions earlier than MySQL 5.0)', 'responder');
                    }
                    echo '</span>';
                    ?>
                </td>
            </tr>
            </tbody></table>
</div>
<style>div#plugin_config-1 td {
    min-width: 100px;
}

div#plugin_config-1 .connected {
    color: lime;
    font-weight: bold;
}
div#plugin_config-1 .not-connected {
    color: red;
    font-weight: bold;
}</style>