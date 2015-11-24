/*Search for sales data by date and hour…avg $ per hour
	In past 3 Mondays, how many sales from 4-6pm?
	Discount in same manner as $ per hour
		Per day, X% of sales were given away as discount
		Avg discount for each type of menu item
		Who gave the discount?
Discounts given and sales & tips per employee hourly / daily as $ and % of sales
% tips and total employee avg tips

Email daily report wants:
Sales per employee: staff name, total sales $, tip $ and % received, discount # % given, more or less than employee evg, datetime
Discount: Staff name, item, dept, category, % $ discount given, datetime*/

SELECT returns,DATENAME(weekday,CONVERT(date,transactionDatetime)) AS dayofWeek FROM transaction WHERE trasactionDatetime BETWEEN ? AND ? ORDER BY dayofWeek ; --grabs total returns based on day of week and date/time...use php to etermiune avg $/hr

SELECT returns,DATENAME(weekday,CONVERT(date,transactionDatetime)) AS dayofWeek FROM transaction WHERE dayofWeek = ? AND trasactionDatetime BETWEEN ? AND ? ; --utility version of the previous statement to grab records by day

--TODO track discounts as % of sales for certain day
--TODO avg discount for each menu item
--TODO track who gives which discount
--TODO tips per employee as % of total tips
--TODO sales # and $ per employee
