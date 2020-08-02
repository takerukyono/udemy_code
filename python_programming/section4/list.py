# セクション4: データ構造
# 16.リスト型

l = [1, 20, 4, 50, 2, 1, 2]
print(l)
print(l[0])
print(l[-2])
print(l[:2])
print(l[2:5])
print(l[2:])
print(l[:])
# print(l[100]) エラー

print(len(l))
print(type(l))
print(list('abcdefg'))

n = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
print(n[::2])
print(n[::-1])

# ネスト
a = ['a', 'b', 'c']
n = [1, 2, 3]
x = [a, n]
print(x)
print(x[0])
print(x[0][1])
print(x[1][2])