function kaplan(data)	{
	
	/* Sort Array */
	function compare(a,b){
		return a[0] - b[0];
	}
	data.sort(compare);
	console.log(data);
	var curve = [];
	curve.push(1);
	var perc = 1;
	var n = data.length;
	var temp = 0;
	var d = n;
	var double = false;
	
	/* Calculate values shown in chart */
	for (var i = 0; i < (data[n-1][0]);){
		if (data[temp][0] == i)	{			
			
			if(data[temp][1] == 1)	{
				perc = perc * ((d-1)/(d--));
				if (double){
					curve.pop();
				}
				curve.push(perc);
				temp++;
				i++;
			
			} else	{
				if (double){
					curve.pop();
				}
				curve.push(perc);
				d--;
				temp++;
				i++;
			}
			for (var j = temp-1; j < n; j++)	{
				if (data[j][0] == (i-1))	{
					i--;
					double = true;
					j = n;
				} else	{
					double = false;
				}
			}
			
	} else	{

		curve.push(perc);
		i++;
	}
	
	}
	curve.pop();
	curve.push(0);
	return curve;
}