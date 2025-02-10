<?php

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Liste des mots vides
        $liste_mots_vides = file('texte_mot_vide.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Lecture du texte ou fichier uploadé
        $texte = "";
        if (!empty($_FILES['file']['tmp_name'])) {
            $fileType = mime_content_type($_FILES['file']['tmp_name']);
            if ($fileType === 'text/plain') {
                $texte = file_get_contents($_FILES['file']['tmp_name']);
            } 
        } 
        elseif (!empty($_POST['zone_Texte'])) {
            $texte = $_POST['zone_Texte'];
        }

        // Nettoyage du texte
        function nettoyer_texte($texte, $liste_mots_vides, $retirer_stopwords) {
            $texte = strtolower($texte);  
            $mots = preg_split('/[\s,.\'"\-?!;:()]+/', $texte);
            return array_filter($mots, function($mot) use ($liste_mots_vides, $retirer_stopwords) {
                return strlen($mot) > 2 && (!$retirer_stopwords || !in_array($mot, $liste_mots_vides));
            });
        } 
        
        //Si l'utilisateur souhaite retirer les mots vides
        if (isset($_POST['stopWords'])) {
            if ($_POST['stopWords'] === 'on') {
                $retirer_stopwords = true;
            } else {
                $retirer_stopwords = false;
            }
        } else {
            $retirer_stopwords = false;
        }
        
        $mots = nettoyer_texte($texte, $liste_mots_vides, $retirer_stopwords);

        $tab_frequence_mots = array_count_values($mots);
        arsort($tab_frequence_mots);

        // Limitation du nombre de mots par défaut
        if (isset($_POST['limite_nbMots'])) {
            $max_mots = (int)$_POST['limite_nbMots'];
        } else {
            $max_mots = 100;
        }
        
        $tab_frequence_mots = array_slice($tab_frequence_mots, 0, $max_mots, true);

        //Pour envoyer les données au JavaScript
        header('Content-Type: application/json');
        echo json_encode($tab_frequence_mots);
        exit;
    }
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nuage de Mots</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="library/wordcloud2.js-gh-pages/src/wordcloud2.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container-principal {
            max-width: 900px;
            margin: auto;
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
            position: relative;
        }

        .section-titre_logo {
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }

        .section-titre_logo img {
            max-height: 50px;
            margin-right: 15px; 
        }

        .section-header h1 {
            margin: 0; 
        }

        .sous_titre {
            font-size: 1rem;
            color: #555;
            margin-bottom: 1.5rem;
        }

        .zone_formulaire {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
            background-color: #f8f9fa;
           
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05);
        }

        .section_unique_formulaire {
            flex: 1;
            min-width: 250px;
            padding: 20px;
        }

        #fond_NuageMots {
            display: block;
            margin: 20px auto;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        @media (max-width: 768px) {
            .zone_formulaire {
                flex-direction: column;
            }
        }

        .form-label {
            font-weight: bold;
            color: #333;
        }


        .form-select,
        .form-control {
            font-size: 1.1rem;
        }

        .groupe_btn_toggle .btn {
            font-size: 1.1rem;
            padding: 12px 20px;
            gap: 15px; 
        }


        #vol , #taille {
            width: 100%;
        }

        #zone_fichierTxt {
            margin-top: 20px;
        }

        #zoneTexte {
            height: 250px; 
            margin-top: 20px;
        }

        .groupe_btn {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .groupe_btn .btn {
            padding: 12px 18px; 
        }

        .groupe_btn {
            gap: 15px; 
        }

    </style>
</head>



<body>
    <div class="container-principal">
        <!-- header -->
        <div class="section-header">
            <div class="section-titre_logo">
                <img src="CloudLine_logo.png" alt="Logo">
                <h1>CloudLine</h1>
            </div>
            <p class="sous_titre">Votre Nuage de Mots Comme Vous le Sentez</p>
        </div>


        <!-- Formulaire -->
        <form id="formulaireCreationNuage" method="POST" enctype="multipart/form-data">
            <div class="zone_formulaire">
                <!-- Bloc Texte ou fichier -->
                <div class="section_unique_formulaire">
                    <h3>Entrez votre texte</h3>
                    <p class="sous_titre">Entrez le texte qui sera transformé en nuage de mots</p>
                    <div class="groupe_btn_toggle d-flex" role="group">
                        <button type="button" id="toggleEcrire" class="btn btn-outline-primary active">Écrire Texte</button>
                        <button type="button" id="toggleTeleverserTexte" class="btn btn-outline-primary">Téléverser Texte</button>
                    </div>
                    <textarea id="zoneTexte" name="zone_Texte" class="form-control" rows="6" placeholder="Entrez votre texte ici"></textarea>
                    <input type="file" name="file" id="zone_fichierTxt" class="form-control d-none">
                </div>

                <!-- Options -->
                <div class="section_unique_formulaire">
                    <h3>Options</h3>
                    <p class="sous_titre">Personnalisez votre Nuage de mots</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="selecteurCouleur" class="form-label">Couleur</label>
                            <select id="selecteurCouleur" name="selecteurCouleur" class="form-select">
                                <option value="random">Aléatoire</option>
                                <option value="red">Rouge</option>
                                <option value="blue">Bleu</option>
                                <option value="green">Vert</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="selecteurPolice" class="form-label">Police</label>
                            <select id="selecteurPolice" name="selecteurPolice" class="form-select">
                                <option value="Arial">Arial</option>
                                <option value="Times New Roman">Times New Roman</option>
                                <option value="Comic Sans MS">Comic Sans MS</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="selecteurStyle" class="form-label">Style</label>
                            <select id="selecteurStyle" name="selecteurStyle" class="form-select">
                                <option value="basique">Basique</option>
                                <option value="lanterne">Lanterne</option>
                                <option value="love">Love</option>
                                <option value="taiwan">Taiwan</option>
                                <option value="eolienne">Eolienne</option>
                                <option value="shuriken">Shuriken</option>
                                <option value="carré">Carré</option>
                                <option value="diamant">Diamant</option>
                                <option value="cardioid">Cardioid</option>
                                <option value="triangle">Triangle</option>
                                <option value="pentagon">Pentagon</option>
                                <option value="etoile">Etoile</option>
                                <option value="miserable">Misérable</option>
                          
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vol" class="form-label">Espacement</label>
                            <input type="range" id="vol" name="vol" min="0" max="50" value="10">
                            <span id="valeurEspace">10</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="limite_nbMots" class="form-label">Nombre max de mots</label>
                            <input type="number" id="limite_nbMots" name="limite_nbMots" class="form-control" value="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="taille" class="form-label">Taille des mots</label>
                            <input type="range" id="taille" name="taille" min="0" max="10" value="5">
                            <span id="valeurTaille">5</span>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input type="checkbox" id="stopWords" name="stopWords" class="form-check-input" checked>
                                <label for="stopWords" class="form-check-label">Retirer les stopwords</label>
                            </div>
                        </div>
                    </div>
                    <!-- Boutons -->
                    <div class="groupe_btn">
                        <button type="submit" class="btn btn-primary">Générer</button>
                        <button type="button" id="sauvegardeImage" class="btn btn-success">Enregistrer</button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Canvas -->
        <canvas id="fond_NuageMots" width="850" height="500"></canvas>
    </div>


    <script>
        const sliderEspace = document.getElementById("vol");
        const valeurEspace = document.getElementById("valeurEspace");

        sliderEspace.addEventListener("input", () => {
            valeurEspace.textContent = sliderEspace.value;
        });

        // Taille des mots
        const sliderTaille = document.getElementById("taille");
        const valeurTaille = document.getElementById("valeurTaille");

        sliderTaille.addEventListener("input", () => {
            valeurTaille.textContent = sliderTaille.value;
        });



        //Boutons bascule
        const toggleEcrire = document.getElementById('toggleEcrire');
        const toggleTeleverserTexte = document.getElementById('toggleTeleverserTexte');
        const zoneTexte = document.getElementById('zoneTexte');
        const zone_fichierTxt = document.getElementById('zone_fichierTxt');

        toggleEcrire.addEventListener('click', () => {
            toggleEcrire.classList.add('active');
            toggleTeleverserTexte.classList.remove('active');

            zoneTexte.classList.remove('d-none');
            zoneTexte.removeAttribute('disabled'); 
            zone_fichierTxt.classList.add('d-none');
            zone_fichierTxt.setAttribute('disabled', true);

        });

        toggleTeleverserTexte.addEventListener('click', () => {
            toggleTeleverserTexte.classList.add('active');
            toggleEcrire.classList.remove('active');

            zone_fichierTxt.classList.remove('d-none');
            zone_fichierTxt.removeAttribute('disabled'); 
            zoneTexte.classList.add('d-none');
            zoneTexte.setAttribute('disabled', true);

        });


        const styles = {
            lanterne: {
                gridSize: Math.round(16 * document.getElementById('fond_NuageMots').width / 1024),

                weightFactor: function (size) {
                    return Math.pow(size, 2.3) * document.getElementById('fond_NuageMots').width / 1024;
                },

                fontFamily: 'Finger Paint, cursive, sans-serif',
                color: '#f0f0c0',
                backgroundColor: '#001f00',
            },
            basique: {
            },
            love: {
                gridSize: 16,
                weightFactor: size => Math.pow(size, 2.3) * 800 / 1024,
                fontFamily: 'Times, serif',
                color: (word, weight) => (weight === 12 ? '#f02222' : '#c09292'),
                rotateRatio: 0.5,
                rotationSteps: 2,
                backgroundColor: '#ffe0e0'
            },

            taiwan: {
                gridSize: 4,
                weightFactor: 5,
                fontFamily: 'Hiragino Mincho Pro, serif',
                color: 'random-dark',
                backgroundColor: '#f0f0f0',
                rotateRatio: 0.5,
                rotationSteps: 2,
                ellipticity: 1,
                shape: theta => {
                    const max = 1026;
                    const leng =[290,296,299,301,305,309,311,313,315,316,318,321,325,326,327,328,330,330,331,334,335,338,340,343,343,343,346,349,353,356,360,365,378,380,381,381,381,391,394,394,395,396,400,400,408,405,400,400,400,401,401,403,404,405,408,410,413,414,414,415,416,418,420,423,425,430,435,440,446,456,471,486,544,541,533,532,533,537,540,537,535,535,533,546,543,539,531,529,530,533,529,528,529,522,521,520,509,520,520,533,522,523,526,528,527,532,537,539,539,540,539,538,533,532,524,523,513,503,482,467,443,438,435,431,429,427,426,422,422,426,426,423,419,414,410,407,404,401,396,393,393,395,392,389,388,383,379,378,376,375,372,369,368,359,343,335,332,327,323,314,308,300,294,290,288,289,290,282,275,269,263,257,242,244,237,235,235,232,231,225,224,221,219,218,218,217,217,215,215,214,214,214,214,214,215,215,216,213,213,212,211,209,207,205,204,206,205,205,205,205,204,203,203,201,200,199,197,195,193,192,192,190,189,187,186,186,183,183,182,182,181,179,180,179,178,178,177,177,176,176,176,176,175,175,175,175,175,175,174,174,175,175,175,175,176,177,176,177,177,177,180,179,179,180,179,179,179,178,178,178,178,177,178,177,179,179,179,180,180,181,181,181,183,183,184,184,186,187,189,189,192,195,193,194,193,194,194,191,189,196,195,196,199,200,201,200,200,200,200,202,203,204,205,210,210,210,211,210,214,218,219,226,231,233,235,235,235,235,236,238,240,241,243,245,246,249,249,249,255,257,264,271,272,274,275,276,276,278,285,292,294,296,301,304,313,320,330,333,337,342,345,348,352,358,363,376,386,379,386,387,387,399,402,402,410,415,420,425,430,429,436,435,438,442,447,451,454,455,474,477,481,484,492,486,488,501,509,544,553,552,553,564,579,593,600,627,637,644,644,643,641,640,641,641,643,643,648,651,653,659,671,678,685,690,698,705,711,715,722,729,738,760,770,777,780,788,792,796,800,803,806,808,810,809,815,819,821,823,826,828,830,834,838,849,854,861,884,891,909,932,996,1026,1016,1011,1015,1018,999,987,827,806,779,754,734,727,700,690,686,682,677,675,672,668,665,664,658,641,614,610,609,609,608,596,591,583,577,576,570,561,553,547,539,531,526,525,524,519,513,502,484,480,478,470,464,458,453,450,448,448,445,441,435,431,423,420,411,408,405,398,388,385,385,385,383,379,372,370,369,368,366,367,371,370,367,365,345,343,342,340,336,334,331,329,326,323,323,322,321,321,319,318,315,313,312,309,308,307,306,305,304,303,303,302,302,300,299,299,297,296,294,292,291,290,289,290,291,291,289,289,285,285,286,287,287,288,288,288,288,288,289,288,287,279,275,273,272,272,272,274,274,274,275,275,277,281,284,285,286,286,286,283,280,279,279,280,281,283,284,288,291];
                    return leng[(theta / (2 * Math.PI)) * leng.length | 0] / max;
                }
            },
            eolienne: {
                gridSize: 6,
                weightFactor: 5,
                fontFamily: 'Arial, sans-serif',
                color: 'random-dark',
                backgroundColor: '#ffffff',
                shape: theta => {
                    const t = theta * Math.PI; 
                    return Math.sin(t) ** 3;
                }
            },
            shuriken: {
                shape: (theta) => {
                    const t = theta % (Math.PI / 2);
                    return Math.abs(Math.cos(t));
                }
            },
            carré: { 
                shape:'square'
            },
            diamant: {
                shape:'diamond'
            },
            
            cardioid: {
                shape:'cardioid'
            },

            pentagon: {
                shape:'pentagon'
            },

            etoile: {
                shape:'star'
            },
            
            triangle: {
                shape:'triangle'
            },

            miserable:{
                gridSize: 18,
                weightFactor: 3,
                fontFamily: 'Average, Times, serif',
                color: function() {
                    return (['#d0d0d0', '#e11', '#44f'])[Math.floor(Math.random() * 3)]
                },
                backgroundColor: '#333'
            }
        };


        function creerTooltip() {
            const tooltip = document.createElement('div');
            const zoneTexte = document.getElementById('zoneTexte');
            tooltip.id = 'tooltip';
            tooltip.style.position = 'relative';
            tooltip.style.zIndex = 9999;
            tooltip.style.backgroundColor = '#000';
            tooltip.style.color = '#fff';
            tooltip.style.padding = '5px';
            tooltip.style.borderRadius = '5px';
            tooltip.style.fontSize = '12px';
            zoneTexte.appendChild(tooltip);
            return tooltip;
        }

    
    document.getElementById('formulaireCreationNuage').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch('index.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => genererNuageMots(data));
});



function genererNuageMots(words) {
    const canvas = document.getElementById('fond_NuageMots');
    const style_choisi = document.getElementById('selecteurStyle').value;
    const couleur_choisi = document.getElementById('selecteurCouleur').value;
    const police_choisi = document.getElementById('selecteurPolice').value;
    const espacement = document.getElementById('vol').value;
    const taille = document.getElementById('taille').value;
    //Options de configuration du nuage
    const options = {
        gridSize: espacement,
        weightFactor: taille,
        fontFamily: 'Arial, sans-serif',
        color: 'random-dark',
        backgroundColor: 'white',

        ...styles[style_choisi],
        list: Object.entries(words),
        hover: (item, dimension, event) => {
            const tooltip = document.getElementById('tooltip') || creerTooltip();
            if (item) {
                tooltip.style.left = `${event.clientX}px`;
                tooltip.style.top = `${event.clientY/4}px`;
                tooltip.textContent = `${item[0]} : ${item[1]}`;
                tooltip.style.display = 'block';
            } else {
                tooltip.style.display = 'none';
            }
        }
    };

    // Appliquer couleur et police uniquement si ce n'est pas "love" ou "lanterne" ou "miserable" 
    if (style_choisi !== 'love' && style_choisi !== 'lanterne' && style_choisi !== 'miserable') {
        if (couleur_choisi === 'random') {
            options.color = 'random-dark'; 
        } else {
            options.color = couleur_choisi;
        }
        options.fontFamily = police_choisi;
    }
  
        WordCloud(canvas, options);
    
}

        //transforme en image 
        document.getElementById('sauvegardeImage').addEventListener('click', function() {
            html2canvas(document.getElementById('fond_NuageMots')).then(canvas => {
                const link = document.createElement('a');
                link.download = 'nuage_mots.png';
                link.href = canvas.toDataURL();
                link.click();
            });
        });
    </script>
</body>
</html>
