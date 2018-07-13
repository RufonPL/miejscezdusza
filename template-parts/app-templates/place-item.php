<div class="row place-item transition">
	<a class="nuh" data-ng-href="{{ place.link }}">
	<div class="place-item-image pull-left relative overflow">
		<div class="box-thin-border absolute-cover transition" data-ng-if="place.imageSrc"></div>
		<place-img place="place" data-ng-show="place.imageSrc"></place-img>
	</div>
	</a>
	<div class="place-item-info row relative">
		<div class="place-extras">
			<div class="place-extra inline-block" data-ng-repeat="(key, value) in place.extras">
				<pi-item></pi-item>
			</div>
		</div>
        <div class="place-laurel" data-ng-if="place.hasLaurMonth">
            <img class="month-name" src="{{ place.hasLaurMonth }}">
        </div>
		<a class="nuh" data-ng-href="{{ place.link }}">
		<div class="place-item-header">
			<h2 class="no-margin">{{ place.name }}</h2>
			<h5 class="font1 normal color1 margin-sm" data-ng-if="place.county">Położenie: <strong>{{ place.county }}</strong></h5>
			<p class="no-margin" data-ng-if="place.contact">Kontakt: <strong>{{ place.contact }}</strong></p>
		</div>
		<p class="place-excerpt" data-ng-if="place.excerpt" data-ng-bind-html="place.excerpt"></p>
		<span class="btn btn-info text-uppercase">Zobacz więcej</span>
		</a>
		<span data-ng-if="contestPlace && isVotingActive" class="btn btn-vote text-uppercase" data-ng-click="voteOnPlace()" data-ng-disabled="voted" data-ng-init="hasVoted(place.votedOn)"><i class="fa fa-check fa-18"></i> <span data-ng-if="!voted">Zagłosuj</span><span data-ng-if="voted">Oddałeś głos</span></span>
	</div>
</div>