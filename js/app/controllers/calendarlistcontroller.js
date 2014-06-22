/**
 * ownCloud - Calendar App
 *
 * @author Raghu Nayyar
 * @author Georg Ehrke
 * @copyright 2014 Raghu Nayyar <beingminimal@gmail.com>
 * @copyright 2014 Georg Ehrke <oc.list@georgehrke.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

app.controller('CalendarListController', ['$scope','Restangular','CalendarModel','EventsModel','$routeParams',
	function ($scope,Restangular,CalendarModel,EventsModel,$routeParams) {

		$scope.calendars = CalendarModel.getAll();
		var calendarResource = Restangular.all('calendars');
		// Gets All Calendars.
		calendarResource.getList().then(function (calendars) {
			CalendarModel.addAll(calendars);
		});
		
		$scope.active = false;
		$scope.newcolor = '';
		$scope.newCalendarInputVal = '';

		// Create a New Calendar
		$scope.create = function (newCalendarInputVal, newcolor) {
			var newCalendar = {
				"displayname" : $scope.newCalendarInputVal,
				"color" : $scope.newcolor,
				"components" : {
					"vevent" : true,
					"vjournal" : true,
					"vtodo" : true
				}
			};
			calendarResource.post(newCalendar).then(function (newCalendar) {
				CalendarModel.create(newCalendar);
			});
		};

		$scope.download = function (id) {
			var deletecalendarResource = Restangular.one('calendars/', id, '/export');
			//deletecalendarResource.get(id).then( function (id) {
			//});
		};

		// Sharing Logic Comes Here.
		$scope.share = function (sharewith) {

		};

		$scope.updatecalenderform = function () {
			//calendarResource.post().then(function (calendar) {
			//	CalendarModel.updateIfExists(calendar);
			//});
		};

		// To Delete a Calendar
		$scope.delete = function (id) {
			var calendar = CalendarModel.get(id);
			var delcalendarResource = Restangular.one('calendars',id);
			delcalendarResource.remove().then( function () {
				CalendarModel.remove(calendar);
			});
		};

		$scope.changeview = function (view) {
			CalendarModel.pushtoggleview(view);
		};

		$scope.todayview = function (view) {
			CalendarModel.pushtoggleview(view);
		};

		$scope.settodaytodatepicker = function () {
			CalendarModel.pushtodaydatepicker();
		};

		$scope.addthisevent = function (id) {
			EventsModel.addEvent(id);
		};
		/* Removes Event Sources */
		$scope.addRemoveEventSource = function(newid) {
			var eventSources = [];
			var eventResource = Restangular.one('calendars/' + newid + '/events');
			eventResource.get().then(function(jcalData) {
				eventSource = EventsModel.addalldisplayfigures(jcalData);
				$scope.eventSource = eventSource;
			});
			console.log($scope.eventSource);
			EventsModel.toggleeventSource(eventSources,$scope.eventSource);
		};

	}
]);