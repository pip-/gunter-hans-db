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

SELECT returns,DATENAME(weekday,CONVERT(date,transactionDatetime)) AS dayofWeek FROM transaction WHERE trasactionDatetime BETWEEN ? AND ? ORDER BY dayofWeek ; --grabs total returns based on day of week and date/time...use php to determine avg $/hr

SELECT returns,DATENAME(weekday,CONVERT(date,transactionDatetime)) AS dayofWeek FROM transaction WHERE dayofWeek = ? AND trasactionDatetime BETWEEN ? AND ? ; --utility version of the previous statement to grab records by day


SELECT employee_id,discount FROM
			(
				(
					food AS f JOIN transaction_detail AS td ON f.food_id=td.food_id
				) AS f_td
				JOIN
				(
					transaction_detail AS td JOIN transaction AS t ON t.transaction_id=td.transaction_id
				) AS td_t
			) AS f_td-td_t ON f_td.transaction_id=td_t.transaction_id
			WHERE transactionDatetime BETWEEN ? AND ? 
			ORDER BY employee_id ; --connects food.discount and transaction.employee_id; lists all discounts for each employee for certain timeframe

SELECT employee_name,tips FROM transaction WHERE transactionDatetime BETWEEN ? AND ? ORDER BY employee_name ; --grabs tips each employee makes over certain time period

SELECT employee_name,returns,COUNT(*) FROM ( employee AS e JOIN transaction AS t ON e.employee_id=t.employee_id) WHERE tranactionDatetime BETWEEN ? AND ? ORDER BY employee_name ; --grabs meployee name and each sale $ and # sales
			
--TODO avg discount for each menu item
--unsupported with current db structure

--TODO track discounts as % of sales for certain day
--unsupported with current db structure
