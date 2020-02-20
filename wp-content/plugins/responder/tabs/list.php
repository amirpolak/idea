<h1>נרשמים</h1>


<Label>
בחרו רשימה מבין רשימות רב מסר שלכם וצפו בפרטי הנרשמים

	</Label>

<br><br>

<select class="select_form select2" style="width:300px">
    <?php
    foreach ($this -> alllists as $list){

            foreach ($list as $key => $value){
                if($key == 'ID'){
                    $opt = "<option name='".$value."' id='".$value."' value='".$value."'>";
                }
                elseif($key == 'DESCRIPTION'){
                    $opt .= $value."</option>";
                }

            }
            echo $opt;
    }

    ?> </select>
    
<div id="res_lists_loader" class="res-lists-loader">
<br>
טוען נרשמים...

</div>
    
<div id="results"></div>

<?php $plainUrl = $this->getAjaxUrl('select_form');
$urlWithId = $this->getAjaxUrl('select_form&id=MyId');

// More sophisticated:
$parametrizedUrl = $this->getAjaxUrl('select_form');
?>
<script>

	var g_isSubscribersLoadedOnce = false;

    function ref_list(){
        
        var chosen = jQuery('.select_form option:selected').val();

        g_isSubscribersLoadedOnce = true;
    	jQuery("#res_lists_loader").show();
    	jQuery('#results').hide();

        jQuery.ajax({
            url: "<?php echo $parametrizedUrl; ?>",
            type : 'post',
            data : {
                action : 'select_form',
                id : chosen,
                nonce: g_resNonce
            },
            success : function( response ) {

            	jQuery("#res_lists_loader").hide();
            	jQuery('#results').show();
            	
                jQuery('#results').html(response);
                jQuery('#results table').DataTable( {
                    "language": {
                        "lengthMenu": "מציג _MENU_ תוצאות בעמוד",
                        "zeroRecords": "לא נמצאו תוצאות",
                        "info": "מציג עמוד _PAGE_ מתוך _PAGES_",
                        "infoEmpty": "לא נמצאו תוצאות",
                        "infoFiltered": "(סוננן מ _MAX_ סה״כ תוצאות)",
                        "decimal":        "",
                        "emptyTable":     "אין מידע זמין",
                        "infoPostFix":    "",
                        "thousands":      ",",
                        "loadingRecords": "טוען...",
                        "processing":     "מעבד...",
                        "search":         "חיפוש:",
                        "paginate": {
                            "first":      "רשאון",
                            "last":       "אחרון",
                            "next":       "הבא",
                            "previous":   "קודם"
                        },
                        "aria": {
                            "sortAscending":  ": הפעל למיין את העמודה בסדר עולה",
                            "sortDescending": ": הפעל למיין את העמודה בסדר יורד"
                        }
                    },
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ]

                } );
            }
        });
    }
    jQuery('.select_form').change(function(){
        ref_list();
    });

    jQuery(document).on("responder_subscribers_tab_click",function(){
    	
    	if(g_isSubscribersLoadedOnce == false)
        	ref_list();
    	
    });

    jQuery(document).ready(function(){
        
    	jQuery("select.select2").select2();
    	    
    });
    
    //ref_list();
    
</script>
<style>
    div#results {
        text-align: left;
    }
    div#results td,div#results th {
        padding: 5px;
    }
</style>