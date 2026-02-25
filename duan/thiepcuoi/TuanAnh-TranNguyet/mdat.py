a = 23.95041322
for i in range(1,100000):
    if(round(i*a,8) == int(round(i*a,8))):
        print(i*a,i)
        break