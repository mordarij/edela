/*! angular-d3 - v0.1.1 - 2013-04-19
 * https://github.com/beefsack/angular-d3
 * Copyright (c) 2013 Michael Alexander; Licensed <%= _.pluck(pkg.licenses, "type").join(", ") %
 */
(function() {
  'use strict';
  angular.module('d3.directives', []);
  angular.module('d3', ['d3.directives']);
}());

(function() {
  'use strict';
  angular.module('d3.directives').directive('d3', [
    function() {
      return {
        scope: {
          d3Data: '=',
          d3Renderer: '='
        },
        restrict: 'EAMC',
        link: function(scope, iElement, iAttrs) {


            var el = d3.select(iElement[0]);
          scope.render = function() {
            if (typeof(scope.d3Renderer) === 'function') {
              scope.d3Renderer(el, scope.d3Data);
            }
          };
//          scope.$watch('d3Renderer', scope.render);
          scope.$watch('d3Data', scope.render, true);
        }
      };
    }
  ]);




}());
var myFormatters = d3.locale({
    "decimal": ",",
    "thousands": "\xa0",
    "grouping": [3],
    "currency": ["", " руб."],
    "dateTime": "%A, %e %B %Y г. %X",
    "date": "%d.%m.%Y",
    "time": "%H:%M:%S",
    "periods": ["AM", "PM"],
    "days": ["воскресенье", "понедельник", "вторник", "среда", "четверг", "пятница", "суббота"],
    "shortDays": ["вс", "пн", "вт", "ср", "чт", "пт", "сб"],
    "months": ["января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря"],
    "shortMonths": ["янв", "фев", "мар", "апр", "май", "июн", "июл", "авг", "сен", "окт", "ноя", "дек"]
});
d3.time.format = myFormatters.timeFormat;
