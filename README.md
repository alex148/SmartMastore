# SmartMastore

##Fonction updateContact / addContact / deleteContact 

Type PUT : updateContact
Type POST : addContact
Type POST : deleteContact

HEADERS : 
Content-Type = json
key = 72c5e00cb6c620fa3a8d4277cb84d83c58dea23be4b931dfad9eeff59d5bc6918ac42db511c7856c3b859c8c440924ef

BODY : 

note : si un champs à null, mettre null sans ""

{
	"id" : "",			//obligatoire
	"firstName": "",
	"name": "",
	"mail": "",
	"phone": "",
	"company": "",
	"address": {
    	"id": "",		//si address, obligatoire
		"line1": "",
		"line2": null,
		"zipCode": "",
		"city": ""
	},
	"type": {			
    	"id": "",		//si type, obligatoire (peut poser problème sans)
		"name": ""
	},
    "exchangeId": ""		//obligatoire
}


##Fonction getAllContact :

Type GET : getAllContact

HEADER : 
key = 72c5e00cb6c620fa3a8d4277cb84d83c58dea23be4b931dfad9eeff59d5bc6918ac42db511c7856c3b859c8c440924ef

Renvoi une liste de tous les contacts sous la forme :
[
{
	"id" : "",			
	"firstName": "",
	"name": "",
	"mail": "",
	"phone": "",
	"company": "",
	"address": {
    	"id": "",		
		"line1": "",
		"line2": null,
		"zipCode": "",
		"city": ""
	},
	"type": {			
    	"id": "",		
		"name": ""
	},
    "exchangeId": ""		
},
{
	"id" : "",			
	"firstName": "",
	"name": "",
	"mail": "",
	"phone": "",
	"company": "",
	"address": {
    	"id": "",		
		"line1": "",
		"line2": null,
		"zipCode": "",
		"city": ""
	},
	"type": {			
    	"id": "",		
		"name": ""
	},
    "exchangeId": ""		
},
{...}
]

