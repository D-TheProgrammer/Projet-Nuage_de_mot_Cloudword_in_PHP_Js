# Projet-Nuage_de_mot_Cloudword_in_PHP_Js

# CloudLine - Générateur de Nuages de Mots  
[French] Application web permettant de générer des nuages de mots à partir de textes ou de fichiers téléversés.  
[English] A web application to generate word clouds from text or uploaded files.  

---

## SOMMAIRE / SUMMARY  
- [Présentation en français / Presentation in French](#français)  
- [Présentation en anglais / Presentation in English](#english)  
- [Tutoriel et démonstration / Tutorial and demonstration](#démo-et-tutoriel--demo-and-tutorial)  

---

## __[FRANÇAIS]__  
### Présentation  
CloudLine est une application web permettant de générer des nuages de mots personnalisés à partir d'un texte saisi ou d'un fichier importé. L'utilisateur peut choisir les couleurs, la police d'écriture, le style du nuage, l'espacement des mots, et bien plus encore.  

### Fonctionnalités  
-  Saisie de texte ou import de fichiers `.txt`
-   Suppression des mots vides (stopwords)
-   Personnalisation des couleurs, polices, styles et espacement
-   Nombre de mots ajustable
-   Génération dynamique du nuage de mots
-   Téléchargement du nuage sous forme d'image  

### Technologies utilisées  
- **PHP** : Traitement des textes et génération des données du nuage  
- **HTML / CSS (Bootstrap 5.3)** : Interface utilisateur  
- **JavaScript (WordCloud2, HTML2Canvas)** : Génération et sauvegarde du nuage  

### Installation et Utilisation  
1. Télécharger ou cloner le projet :  
```bash
   git clone https://github.com/ton-repo/CloudLine.git
```

2. Lancer le serveur local avec :
```bash
  php -S localhost:8000
```

3. Ouvrir votre navigateur et accéder à :
http://localhost:8000

4.Entrer un texte ou téléverser un fichier .txt.

5.Configurer les options et générer le nuage de mots.






## __[ENGLISH]__  
### Presentation  
CloudLine is a web application that allows you to generate custom word clouds from typed text or uploaded files. The user can customize colors, fonts, word cloud style, word spacing, and much more.  

### Features  
- Input text or upload `.txt` files  
- Remove stopwords  
- Customize colors, fonts, styles, and spacing  
- Adjustable word limit  
- Real-time word cloud generation  
- Download word cloud as an image  

### Technologies Used  
- **PHP**: Text processing and word cloud data generation  
- **HTML / CSS (Bootstrap 5.3)**: User interface  
- **JavaScript (WordCloud2, HTML2Canvas)**: Word cloud generation and image saving  

### Installation and Usage  
1. Download or clone the project:  
```bash
   git clone https://github.com/your-repo/CloudLine.git
```
2.Run the local server with:
 ```bash
php -S localhost:8000
   ```

3. Open your browser and go to:
http://localhost:8000

4. Enter text or upload a .txt file.

5. Configure the options and generate the word cloud.

## __[Démo et Tutoriel / Demo and Tutorial]__
#### Utilisez l'interface pour définir vos besoin de votre texte ou du fichier télerverser (forme - taille - police - nombre de mot - mot vide ou pas - couleur  )  /Use the interface to set your requirements for your text or the upload file  (shape - size - font - word count - stopwords or not - color)
<div align="center">
  <img width="946" alt="image" src="https://github.com/user-attachments/assets/26e1387d-23a7-4d8a-a28b-6325e3b1331f"
">
</div>

#### Dans cet exemple, un texte a été téléversé / In this example, a text has been uploaded.
<div align="center">
<img width="946" alt="image" src="https://github.com/user-attachments/assets/831ea24d-7e87-4893-bdde-b94f0cdd5b47">

</div>


#### Après avoir réglé et choisi, appuyez sur 'Générer'. Le nuage de mots apparaît, et il est possible de télécharger l'image avec le bouton "Enregistrer". / After setting and choosing, click on 'Generate'. The word cloud will appear, and it is possible to download the image by using the button "Enregistrer"
<div align="center">
  <img width="346" alt="image" src="https://github.com/user-attachments/assets/4bbb7093-6c4a-4119-805b-883e5a18b3d0">
  <img width="346" alt="image" src="https://github.com/user-attachments/assets/9c0f4867-9b66-4f7d-b1b1-48ad69193c4a">
</div>


