		<div id="search">
			<form action="<?php echo menu_getStraLink(menu_getVerzLabel("query_id"));?>" method="get" id="formQuery">
				<p><?php echo menu_getVerzLabel("query_headline");?></p>
				<fieldset>
					<input name="SearchText" type="text" class="forma" size="12" value="<?php echo header_getQueryParameter();?>"/>
					<input name="image" type="image" src="/images/basic/nadji.jpg" alt="<?php echo menu_getVerzLabel("query_start");?>"/>
				</fieldset>
			</form>
		</div>

		<div id="tools">
			<a href="javascript:sendPage('<?php echo menu_getVerzLabel("mail_headline"); ?>');"><?php echo menu_getVerzLabel("send_to_friend_headline"); ?></a> | 
			<a href="<?php echo menu_getStraLink(menu_getVerzPocetna());?>"><?php echo menu_getVerzLabel("home_headline"); ?></a> | 
			<a href="<?php echo menu_getStraLink(menu_getVerzLabel("map_id"));?>"><?php echo menu_getVerzLabel("map_headline"); ?></a> | 
			<a href="javascript:printURL();"><?php echo menu_getVerzLabel("print_page_headline"); ?></a>
		</div>

		<div id="additional_nav">
			<?php echo $menu->getAdditional();?>
		</div>

		<div id="path_nav">
			<?php echo $menu->getPath();?>
		</div>
