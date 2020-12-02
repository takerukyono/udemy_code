# セクション5: 制御フローとコード構造
# 46. zip関数

days = ['Mon', 'Tue', 'Wed']
fruits = ['apple', 'banana', 'orange']
drinks =  ['coffee', 'tea', 'bear']

"""
for i in range(len(days)):
    print(days[i], fruits[i], drinks[i])
"""

for day, fruit, drink in zip(days, fruits, drinks):
    print(day, fruit, drink)

