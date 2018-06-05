<div class="row region-item relative transition">
	<a href="{{ singleItem.link }}" class="nuh">
	<div class="region-item-mask absolute-cover transition"></div>
	<img src="{{ singleItem.imageSrc }}" alt="{{ singleItem.imageAlt }}">
	<div class="region-item-info absolute-center-both">
		<h2 class="text-uppercase color3" data-ng-if="itemType=='region'">{{ singleItem.name }}</h2>
		<h2 class="text-uppercase color3 no-margin" data-ng-if="itemType=='month'">Miejsce miesiąca - {{ singleItem.month }} {{ singleItem.year }}</h2>
		<h2 class="text-uppercase color3 no-margin" data-ng-if="itemType=='year'">Miejsce roku - {{ singleItem.year }}</h2>
		<h2 class="text-uppercase color3 normal margin-sm lh18" data-ng-if="itemType=='month' || itemType=='year'">{{ singleItem.name }}</h2>
		<h3 class="text-uppercase color3 normal margin-sm f18" data-ng-if="itemType=='month' || itemType=='year'">{{ singleItem.county }}</h3>
		<span class="btn btn-primary text-uppercase btn-md">Zobacz więcej</span>
	</div>
	</a>
</div>