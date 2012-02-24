function init(){
	
	$("#search").live("click",function(){
		$("#twitterDataChart").empty();
		$.ajax({
    			type: "GET",
    			url: "twitterajax.php",
    			data: {
    				"q":$("#searchbox").val()
				},
    				
    			success: function(r){
    				$("#twitterDataChart").append(r);
    				plotGraph(twitterData);
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
			});
	});
	
	function plotGraph(twitterData){
		$("#twitterDataChart").empty();
		 	var plot = $.jqplot('twitterDataChart',  [twitterData],
			{ title:'Weight',
  				axes:{
  					xaxis:{renderer:$.jqplot.DateAxisRenderer,
          					tickOptions:{
            					formatString:'%m-%d %H'}},
  					yaxis:{label: "number of tweets"}},
  					highlighter: {
        				show: true,
        				sizeAdjust: 7.5
      					},
  				series:[], seriesDefaults: {
        trendline: {
            show: true,
            color: '#666666'
        }
    }
			});//replot?
		
			
	}
	


}