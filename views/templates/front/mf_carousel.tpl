{*
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Selwyn Orren
 *  @copyright 2017 Linuxweb
 *  @license   LICENSE.md
 *}
<script type="text/javascript">
var MOBILE = "{$MF_PER_ROW_MOBILE}";
var TABLET = "{$MF_PER_ROW_TABLET}";
var DESKTOP = "{$MF_PER_ROW_DESKTOP}";
</script>
{if $manufacturers}
	<div class="tm-home col-xs-12">
		<div class="tm-hp text-center">
			<h2><span class="tm-over">Our <span> Favourite </span>Brands</span></h2>
			<p>{$MF_DESCRIPTION|escape:'htmlall':'UTF-8'}</p>
		</div>
		<div class="col-xs-12 col-sm-12">
			<div class="owl-carousel owl-theme">
			{foreach from=$manufacturers item=manufacturer}
				<div>
					<a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}">
						<img src="{$manufacturer.image|escape:'htmlall':'UTF-8'}" title="{$manufacturer.name|escape:'html':'UTF-8'}" alt="{$manufacturer.name|escape:'htmlall':'UTF-8'}">
					</a>
					{if $MF_SHOW_MAN_NAME}<a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'htmlall':'UTF-8'}"><h5>{$manufacturer.name|escape:'htmlall':'UTF-8'}</h5></a>{/if}
				</div>
			{/foreach}
			</div>
		</div>
	</div>
{/if}