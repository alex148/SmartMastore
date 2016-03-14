# SmartMastore


Permet de synchroniser des contacts entre une base de données Exchange et MySql. Objectif : gérer une liste de contact sur une application afin de les trouver grace à un moteur de recherche (liste de contact / map)

SmartMastore : WebService rest (JSON) basé sur SlimFramework + PHP-EWS (https://github.com/jamesiarmes/php-ews/). Permet de :
Récupérer tous les contacts sur la base MySql
Mettre à jour un contact (Exchange + Mysql)
Ajouter un contact (Exchange + MySql)
Supprimer un contact (Exchange + MySql)
Synchroniser les deux bases (en cas de modification directe sur Exchange)

##Fonction updateContact / addContact / deleteContact 

Type PUT : updateContact
Type POST : addContact
Type POST : deleteContact

HEADERS : 
Content-Type = json
key = 'security key'

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
key = 'security key'

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

