var bargraphSeries = [];
var bargraphTitles = [];
var bargraphTooltips = [];
var bargraphProfileIds = [];

var bargraph = (function basic_bars(container, data)
{
   var
      d1 = [],                                  // First data series
      d2 = [],                                  // Second data series
      point,                                    // Data point variable declaration
      i;

   var max = 1000;
   for(i in data) max = Math.max(max, data[i]._y);

   // renormalizes the data to a 100-pt scale

   for (i in data)
   {
      data[i]._ypos = data[i]._y / max * 1000;
      if (data[i].highlighted)
         d2.push([parseInt(i, 10), data[i]._ypos]);
      else
         d1.push([parseInt(i, 10), data[i]._ypos]);
   }

   function formatter(pos)
   {
      var i = Math.floor(pos.x);
      var title = bargraphTitles[i];
      var tooltip = bargraphTooltips[i];
      
      return "<div class='graph-tooltip'><h3>" + title + "</h3><span>" + tooltip + "</span></div>";
   }
              
   // Draw the graph
  
   Flotr.draw(
      container,
      [d1, d2],
      {
         colors: ["#b9b9b9", "#1aab0a"],
         grid: { color: null, horizontalLines: false, verticalLines: false},
         bars : {
            show : true,
            horizontal : false,
            shadowSize : 0,
            barWidth : 0.9,
            lineWidth: 1,
            fillOpacity: 0.5
         },
         mouse : {
            track : true,
            relative : true,
            lineColor: "#0080c5",
            fillColor: "#0080c5",
            fillOpacity: 0.8,
            trackFormatter: formatter,
            pos:'nw'
         },
         xaxis: {
            showLabels: false
         },
         yaxis : {
            min : 0,
            autoscaleMargin : 1,
            showLabels: false,
            max : 1000
         }
      }
   );
});


var graphAnimTimeout;

var animGraph = function(target, start_data, end_data, speed)
{
   clearTimeout(graphAnimTimeout);

   if (!start_data)
      start_data = [];

   var valid = 0;

   for (var i in end_data)
   {
      if (!start_data[i])
         start_data[i] = { _y: 0, highlighted: end_data[i].highlighted };
         
      if (Math.abs(end_data[i]._y - start_data[i]._y) > 1)
      {
         start_data[i]._y +=  (end_data[i]._y - start_data[i]._y) / speed;
      }
      else
      {
         start_data[i]._y = end_data[i]._y;
         valid += 1;
      }
   }
   
   bargraph(target, start_data);
   
   if (valid === end_data.length)
      return;

   graphAnimTimeout = setTimeout(function(){ animGraph(target, start_data, end_data, speed); }, 10);
};

$(window).resize(function()
{
   bargraph(document.getElementById("graph-content"), bargraphSeries);
});
   
$(document).ready(function()
{
   animGraph(document.getElementById("graph-content"), null, bargraphSeries, 20);
});
