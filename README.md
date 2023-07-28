**SalvaPassword - Secure Password Manager**

## Descrizione
SalvaPassword è un gestore di password sicuro che ti aiuta a mantenere tutte le tue password in un unico luogo, al sicuro e facilmente accessibile. Con SalvaPassword, puoi generare password sicure, crittografarle e accedervi facilmente quando ne hai bisogno, il tutto utilizzando l'autenticazione a due fattori (2FA) per una maggiore sicurezza.

## Caratteristiche principali

- **Gestione delle password**: Salva e gestisci tutte le tue password in un unico posto. Non dovrai più ricordare password complesse o utilizzare le stesse password per più siti.
- **Generatore di password**: Crea password forti e sicure utilizzando il generatore di password integrato.
- **Crittografia avanzata**: Tutte le password vengono crittografate per garantire la massima sicurezza.
- **Autenticazione a due fattori (2FA)**: Proteggi il tuo account utilizzando l'autenticazione a due fattori per prevenire l'accesso non autorizzato.
- **Interfaccia user-friendly**: Un'interfaccia semplice ed intuitiva rende l'utilizzo di SalvaPassword facile per tutti.
- **Tema scuro**: Un design moderno e piacevole con tema scuro per un'esperienza visiva gradevole.
- **Supporto multi-piattaforma**: Accedi al tuo account da qualsiasi dispositivo, sia esso desktop o mobile.

## Requisiti di sistema
- Web server (Apache, Nginx, etc.)
- PHP 7.0 o versione successiva
- MySQL o un altro database compatibile

## Installazione
1. Clona il repository da GitHub.
2. Configura il tuo server web per servire il progetto dalla directory corretta.
3. Imposta le credenziali di accesso al database nel file `config.php`.
4. Importa il file `database.sql` nel tuo database per creare la tabella necessaria.
5. Assicurati che il server web abbia i permessi corretti per accedere alla directory di upload (se presente).
6. Assicurati che il server web abbia i permessi corretti per scrivere nella directory di log (se presente).

## Configurazione
Modifica le seguenti variabili nel file `config.php` per adattarle alle tue esigenze:

```php
// Configurazione database
$host = 'localhost';
$username = 'username';
$password = 'password';
$database = 'salvapassword';

// Chiave di crittografia (assicurati di utilizzare una chiave univoca)
$encryptionKey = 'inserisci_una_chiave_di_crittografia';

// Durata della sessione (in secondi)
$sessionDuration = 86400; // 24 ore
```

## Licenza
Questo progetto è rilasciato con licenza MIT. Vedi il file [LICENSE](LICENSE) per ulteriori dettagli.

## Contatti
Per qualsiasi domanda o suggerimento, puoi visitare il sito web https://passwords.digitcom-informatica.com/.

## Contributi
Se desideri contribuire a questo progetto, sei il benvenuto! Puoi seguire questi passaggi per iniziare:

1. Forka il repository.
2. Crea un nuovo branch con la tua modifica (`git checkout -b miglioramento-caratteristica`).
3. Commita le tue modifiche (`git commit -m 'Aggiunta nuova funzionalità'`).
4. Pusha il branch (`git push origin miglioramento-caratteristica`).
5. Apri una pull request.

## Ringraziamenti
Un ringraziamento speciale a tutti coloro che hanno contribuito a rendere questo progetto possibile!
