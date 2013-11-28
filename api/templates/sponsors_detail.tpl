<div class="el-ajax-detail">
	<div class="block" rel="clinic">
		<div class='block-header red'>
			<span><!--$name!--></span>
		</div>
		<div class='shadow'></div>
		<div class="el-ditem el-ditem-h">
			<div class="logo el-col">
				<!--if ($img)!-->
				<!--$img!-->
				<!--endif!-->
			</div>
			<div class="el-scroll">
				<div class="el-scroll-in">
				</div>
			</div>
			<h3>О компании</h3>
			<p><!--$detail_text!--></p>
			<h3>Контактные данные</h3>
			<div class="el-table">
				<table>
					<!--if ($address)!-->
					<tr>
						<td class="t">Адрес:</td>
						<td class="d">
							<!--$address!-->
						</td>
					</tr>
					<!--endif!-->
					<!--if ($phone)!-->
					<tr>
						<td class="t">Телефон:</td>
						<td class="d">
							<!--$phone!-->
						</td>
					</tr>
					<!--endif!-->
					<!--if ($fax)!-->
					<tr>
						<td class="t">Факс:</td>
						<td class="d">
							<!--$fax!-->
						</td>
					</tr>
					<!--endif!-->
					<!--if ($email)!-->
					<tr>
						<td class="t">E-mail:</td>
						<td class="d">
							<!--$email!-->
						</td>
					</tr>
					<!--endif!-->
					<!--if ($web)!-->
					<tr>
						<td class="t">Официальный сайт:</td>
						<td class="d">
							<a href="<!--$web!-->"><!--$web_short!--></a>
						</td>
					</tr>
					<!--endif!-->
				</table>
			</div>
		</div>
	</div>
</div>