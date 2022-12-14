import mysql.connector

import sys
import hashlib
import codecs


if sys.version_info < (3, 6):
    import sha3



# ajout password !!
mydb = mysql.connector.connect(
  host="zefresk.com",
  user=" mamazon",
  #password="",
  database="mamazon",
  port= 3306
)


mycursor = mydb.cursor()



data = [
    {
        "nom": "Denton",
        "prenom": "Hoffman",
        "login": "GDX16SJH5KQ",
        "hpass": "YNJ16LQV1EI",
        "privileges": "2"
    },
    {
        "nom": "Yoshi",
        "prenom": "Goff",
        "login": "DVV58LRP5QP",
        "hpass": "HDS45NTA5LB",
        "privileges": "0"
    },
    {
        "nom": "Cailin",
        "prenom": "Richmond",
        "login": "WWE72WWF8JG",
        "hpass": "RQU93CDT3UZ",
        "privileges": "3"
    },
    {
        "nom": "Noelani",
        "prenom": "Kirk",
        "login": "SEG61PLN4QF",
        "hpass": "SHW42PKH5VB",
        "privileges": "5"
    },
    {
        "nom": "Suki",
        "prenom": "Cox",
        "login": "PFN86DZW4BH",
        "hpass": "SCR50NLT5PD",
        "privileges": "1"
    }
]



######## hash and insert

for x in data :
    str = "mamazon.zefresk.com#"+x['hpass']
    x['hpass']  = hashlib.new("sha3_512", str.encode()).digest()


newdata =[]
for i in data:
    newdata.append(list(i.values()))



sql = "INSERT INTO utilisateurs (nom, prenom,login,hpass,privileges) VALUES (%s, %s, %s ,%s ,%s)"

mycursor.executemany(sql, newdata)

mydb.commit()

print(mycursor.rowcount, "was inserted.")
