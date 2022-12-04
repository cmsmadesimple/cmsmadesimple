{form_start}

{capture assign='value'}<p><img src='{uploads_url}/images/logo1.gif' style="float: right;" />Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris et ipsum id ante dignissim cursus sollicitudin eget erat. Quisque sit amet arcu urna. Nulla ultricies lacinia sapien, sed aliquam quam feugiat in. Donec consectetur pretium congue. Integer aliquam facilisis lacus, ut facilisis erat pharetra eget. Duis dapibus posuere nunc, id gravida massa pellentesque ac. Duis massa lectus, tempor sed imperdiet aliquam, luctus ut risus. Integer nisl libero, porttitor sit amet sagittis at, sodales at urna. Maecenas facilisis arcu eget nulla imperdiet sed interdum massa pretium. In id eros orci, pharetra dignissim nisl. Quisque vitae luctus turpis. Aenean pulvinar accumsan justo, vel pulvinar mi consequat in. Vestibulum ac turpis vel massa venenatis volutpat placerat in diam. Quisque ac magna dolor. Aliquam sagittis interdum urna a euismod. </p>{/capture}

{cms_textarea forcemodule='MicroTiny' name='mt_example' id='mt_example' enablewysiwyg=1 rows=10 columns=80 value=$value}

{form_end}