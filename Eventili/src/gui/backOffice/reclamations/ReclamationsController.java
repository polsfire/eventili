/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package gui.backOffice.reclamations;

import de.jensd.fx.glyphs.fontawesome.FontAwesomeIcon;
import de.jensd.fx.glyphs.fontawesome.FontAwesomeIconView;
import entities.Personne;
import entities.reclamation;
import gui.sigleton.singleton;
import gui.singleton.SingletonReclam;
import java.io.IOException;
import java.net.URL;
import java.time.format.DateTimeFormatter;
import java.time.format.DateTimeParseException;
import java.time.format.ResolverStyle;
import java.util.List;
import java.util.Optional;
import java.util.ResourceBundle;
import javafx.application.Platform;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.fxml.Initializable;
import javafx.geometry.HPos;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.geometry.VPos;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.layout.Background;
import javafx.scene.layout.ColumnConstraints;
import javafx.scene.layout.GridPane;
import javafx.scene.layout.Pane;
import javafx.scene.layout.RowConstraints;
import javafx.scene.paint.Color;
import javafx.stage.Stage;
import services.ReclamationService;

/**
 * FXML Controller class
 *
 * @author chaim
 */
public class ReclamationsController implements Initializable {

    @FXML
    private Button OuverButtonFiltre;
    @FXML
    private Button ClotureButtonFiltre;
    @FXML
    private Button EnAttenteButtonFiltre;
    @FXML
    private Button AucunFiltreButton;
    @FXML
    private GridPane grid;
    @FXML
    private Pane pane;
    private ReclamationService rs = new ReclamationService();
    private String ActiveFilter = "";
    private boolean filterIsActive = false;
    private int autoupdate = 0;
    //singleton data = singleton.getInstance();
    //Personne P = data.getUser();

    /**
     * Initializes the controller class.
     */
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        Platform.runLater(new Runnable() {
            @Override
            public void run() {
                autoupdate = rs.getAll().size();
                if (rs.getAll().isEmpty()) {
                    EmptyDatabase();
                } else {
                    populateView();
                }
                autoUpdate();
                
               populateView();
            }
        });

    }

    private void autoUpdate() {
        pane.hoverProperty().addListener(l -> {
            if (autoupdate != rs.getAll().size()) {
                if (rs.getAll().isEmpty()) {
                    EmptyDatabase();
                } else if (filterIsActive) {
                    populateViewFilter(ActiveFilter);
                } else {
                    populateView();
                }
                autoupdate = rs.getAll().size();

            }
        });
    }

    /*
        Filter Buttons
     */
    @FXML
    private void OuverButtonFiltreFunction(ActionEvent event) {
        if (OuverButtonFiltre.getStyleClass().contains("OuvertFiltreButtonUnactive")) {
            OuverButtonFiltre.getStyleClass().remove("OuvertFiltreButtonUnactive");
            OuverButtonFiltre.getStyleClass().add("OuvertFiltreButtonActive");
        }
        if (OuverButtonFiltre.getStyleClass().contains("OuvertFiltreButton")) {
            OuverButtonFiltre.getStyleClass().remove("OuvertFiltreButton");
            OuverButtonFiltre.getStyleClass().add("OuvertFiltreButtonActive");
        }
        if (ClotureButtonFiltre.getStyleClass().contains("ClotureFiltreButtonActive")) {
            ClotureButtonFiltre.getStyleClass().remove("ClotureFiltreButtonActive");
            ClotureButtonFiltre.getStyleClass().add("ClotureFiltreButtonUnactive");
        }
        if (ClotureButtonFiltre.getStyleClass().contains("ClotureFiltreButton")) {
            ClotureButtonFiltre.getStyleClass().remove("ClotureFiltreButton");
            ClotureButtonFiltre.getStyleClass().add("ClotureFiltreButtonUnactive");
        }
        if (EnAttenteButtonFiltre.getStyleClass().contains("EnAttenteFiltreButtonActive")) {
            EnAttenteButtonFiltre.getStyleClass().remove("EnAttenteFiltreButtonActive");
            EnAttenteButtonFiltre.getStyleClass().add("EnAttenteFiltreButtonUnactive");
        }
        if (EnAttenteButtonFiltre.getStyleClass().contains("EnAttenteFiltreButton")) {
            EnAttenteButtonFiltre.getStyleClass().remove("EnAttenteFiltreButton");
            EnAttenteButtonFiltre.getStyleClass().add("EnAttenteFiltreButtonUnactive");
        }
        if (AucunFiltreButton.getStyleClass().contains("AucunFiltreButton")) {
            AucunFiltreButton.getStyleClass().remove("AucunFiltreButton");
            AucunFiltreButton.getStyleClass().add("AucunFiltreButtonUnactive");
        }
        if (AucunFiltreButton.getStyleClass().contains("AucunFiltreButtonActive")) {
            AucunFiltreButton.getStyleClass().remove("AucunFiltreButtonActive");
            AucunFiltreButton.getStyleClass().add("AucunFiltreButtonUnactive");
        }
        ActiveFilter = "ouvert";
        filterIsActive = true;
        populateViewFilter("ouvert");
    }

    @FXML
    private void ClotureButtonFiltreFunction(ActionEvent event) {
        if (OuverButtonFiltre.getStyleClass().contains("OuvertFiltreButtonActive")) {
            OuverButtonFiltre.getStyleClass().remove("OuvertFiltreButtonActive");
            OuverButtonFiltre.getStyleClass().add("OuvertFiltreButtonUnactive");
        }
        if (OuverButtonFiltre.getStyleClass().contains("OuvertFiltreButton")) {
            OuverButtonFiltre.getStyleClass().remove("OuvertFiltreButton");
            OuverButtonFiltre.getStyleClass().add("OuvertFiltreButtonUnactive");
        }
        if (ClotureButtonFiltre.getStyleClass().contains("ClotureFiltreButtonUnactive")) {
            ClotureButtonFiltre.getStyleClass().remove("ClotureFiltreButtonUnactive");
            ClotureButtonFiltre.getStyleClass().add("ClotureFiltreButtonActive");
        }
        if (ClotureButtonFiltre.getStyleClass().contains("ClotureFiltreButton")) {
            ClotureButtonFiltre.getStyleClass().remove("ClotureFiltreButton");
            ClotureButtonFiltre.getStyleClass().add("ClotureFiltreButtonActive");
        }
        if (EnAttenteButtonFiltre.getStyleClass().contains("EnAttenteFiltreButtonActive")) {
            EnAttenteButtonFiltre.getStyleClass().remove("EnAttenteFiltreButtonActive");
            EnAttenteButtonFiltre.getStyleClass().add("EnAttenteFiltreButtonUnactive");
        }
        if (EnAttenteButtonFiltre.getStyleClass().contains("EnAttenteFiltreButton")) {
            EnAttenteButtonFiltre.getStyleClass().remove("EnAttenteFiltreButton");
            EnAttenteButtonFiltre.getStyleClass().add("EnAttenteFiltreButtonUnactive");
        }
        if (AucunFiltreButton.getStyleClass().contains("AucunFiltreButton")) {
            AucunFiltreButton.getStyleClass().remove("AucunFiltreButton");
            AucunFiltreButton.getStyleClass().add("AucunFiltreButtonUnactive");
        }
        if (AucunFiltreButton.getStyleClass().contains("AucunFiltreButtonActive")) {
            AucunFiltreButton.getStyleClass().remove("AucunFiltreButtonActive");
            AucunFiltreButton.getStyleClass().add("AucunFiltreButtonUnactive");
        }
        ActiveFilter = "cloturer";
        filterIsActive = true;
        populateViewFilter("cloturer");
    }

    @FXML
    private void EnAttenteButtonFiltreFunction(ActionEvent event) {
        if (OuverButtonFiltre.getStyleClass().contains("OuvertFiltreButtonActive")) {
            OuverButtonFiltre.getStyleClass().remove("OuvertFiltreButtonActive");
            OuverButtonFiltre.getStyleClass().add("OuvertFiltreButtonUnactive");
        }
        if (OuverButtonFiltre.getStyleClass().contains("OuvertFiltreButton")) {
            OuverButtonFiltre.getStyleClass().remove("OuvertFiltreButton");
            OuverButtonFiltre.getStyleClass().add("OuvertFiltreButtonUnactive");
        }
        if (ClotureButtonFiltre.getStyleClass().contains("ClotureFiltreButtonActive")) {
            ClotureButtonFiltre.getStyleClass().remove("ClotureFiltreButtonActive");
            ClotureButtonFiltre.getStyleClass().add("ClotureFiltreButtonUnactive");
        }
        if (ClotureButtonFiltre.getStyleClass().contains("ClotureFiltreButton")) {
            ClotureButtonFiltre.getStyleClass().remove("ClotureFiltreButton");
            ClotureButtonFiltre.getStyleClass().add("ClotureFiltreButtonUnactive");
        }
        if (EnAttenteButtonFiltre.getStyleClass().contains("EnAttenteFiltreButtonUnactive")) {
            EnAttenteButtonFiltre.getStyleClass().remove("EnAttenteFiltreButtonUnactive");
            EnAttenteButtonFiltre.getStyleClass().add("EnAttenteFiltreButtonActive");
        }
        if (EnAttenteButtonFiltre.getStyleClass().contains("EnAttenteFiltreButtonUnactive")) {
            EnAttenteButtonFiltre.getStyleClass().remove("EnAttenteFiltreButton");
            EnAttenteButtonFiltre.getStyleClass().add("EnAttenteFiltreButtonActive");
        }
        if (AucunFiltreButton.getStyleClass().contains("AucunFiltreButton")) {
            AucunFiltreButton.getStyleClass().remove("AucunFiltreButton");
            AucunFiltreButton.getStyleClass().add("AucunFiltreButtonUnactive");
        }
        if (AucunFiltreButton.getStyleClass().contains("AucunFiltreButtonActive")) {
            AucunFiltreButton.getStyleClass().remove("AucunFiltreButtonActive");
            AucunFiltreButton.getStyleClass().add("AucunFiltreButtonUnactive");
        }
        ActiveFilter = "EnAttenteUser";
        filterIsActive = true;
        populateViewFilter("EnAttenteUser");
    }

    @FXML
    private void AucunFiltreButtonFunction(ActionEvent event) {
        if (OuverButtonFiltre.getStyleClass().contains("OuvertFiltreButtonActive")) {
            OuverButtonFiltre.getStyleClass().remove("OuvertFiltreButtonActive");
            OuverButtonFiltre.getStyleClass().add("OuvertFiltreButtonUnactive");
        }
        if (OuverButtonFiltre.getStyleClass().contains("OuvertFiltreButton")) {
            OuverButtonFiltre.getStyleClass().remove("OuvertFiltreButton");
            OuverButtonFiltre.getStyleClass().add("OuvertFiltreButtonUnactive");
        }
        if (ClotureButtonFiltre.getStyleClass().contains("ClotureFiltreButtonActive")) {
            ClotureButtonFiltre.getStyleClass().remove("ClotureFiltreButtonActive");
            ClotureButtonFiltre.getStyleClass().add("ClotureFiltreButtonUnactive");
        }
        if (ClotureButtonFiltre.getStyleClass().contains("ClotureFiltreButton")) {
            ClotureButtonFiltre.getStyleClass().remove("ClotureFiltreButton");
            ClotureButtonFiltre.getStyleClass().add("ClotureFiltreButtonUnactive");
        }
        if (EnAttenteButtonFiltre.getStyleClass().contains("EnAttenteFiltreButton")) {
            EnAttenteButtonFiltre.getStyleClass().remove("EnAttenteFiltreButton");
            EnAttenteButtonFiltre.getStyleClass().add("EnAttenteFiltreButtonUnactive");
        }
        if (EnAttenteButtonFiltre.getStyleClass().contains("EnAttenteFiltreButtonActive")) {
            EnAttenteButtonFiltre.getStyleClass().remove("EnAttenteFiltreButton");
            EnAttenteButtonFiltre.getStyleClass().add("EnAttenteFiltreButtonUnactive");
        }
        if (AucunFiltreButton.getStyleClass().contains("AucunFiltreButtonUnactive")) {
            AucunFiltreButton.getStyleClass().remove("AucunFiltreButtonUnactive");
            AucunFiltreButton.getStyleClass().add("AucunFiltreButtonActive");
        }
        if (AucunFiltreButton.getStyleClass().contains("AucunFiltreButton")) {
            AucunFiltreButton.getStyleClass().remove("AucunFiltreButton");
            AucunFiltreButton.getStyleClass().add("AucunFiltreButtonActive");
        }
        ActiveFilter = "";
        filterIsActive = false;
        ReclamationService rs = new ReclamationService();
        if (rs.getAll().isEmpty()) {
            EmptyDatabase();
        } else {
            populateView();
        }
    }

    /*
    Populate View
     */
    private void EmptyDatabase() {
        clearGrid();
        Label EmptyDatabase = new Label();
        EmptyDatabase.setText("Database is Empty / Incorect Filter");
        EmptyDatabase.setTextFill(Color.web("red"));
        EmptyDatabase.setMinWidth(Control.USE_PREF_SIZE);
        ColumnConstraints column1 = new ColumnConstraints();
        column1.setHalignment(HPos.CENTER);
        grid.getColumnConstraints().add(column1);
        grid.setAlignment(Pos.CENTER);
        grid.setBackground(Background.EMPTY);
        grid.getChildren().add(EmptyDatabase);
    }

    private void clearGrid() {
        grid.getChildren().clear();
        grid.getColumnConstraints().clear();
        grid.getRowConstraints().clear();
    }

    private void Supprimer(reclamation reclam) {
        Alert delete = new Alert(AlertType.WARNING);
        delete.setTitle("Confirmer Supression");
        delete.setContentText("Voulez vous confirmer la suppression de la Reclamation ?\n"
                + "Titre : " + reclam.getTitre()
                + "\nNom Prenom : " + reclam.getP().getNom_pers() + " " + reclam.getP().getPrenom_pers()
                + "\nDescription : " + reclam.getDescription()
                + "\nDate Envoie : " + reclam.getTimeStamp().toLocalDateTime().format(DateTimeFormatter.ofPattern("d MMM uuuu"))
                + "\n Status : " + reclam.getStatus()
        );
        ButtonType oui = new ButtonType("Confirmer");
        ButtonType non = new ButtonType("Annuler");
        delete.getButtonTypes().clear();
        delete.getButtonTypes().addAll(oui, non);
        Optional<ButtonType> result = delete.showAndWait();

        if (result.get() == oui) {
            rs.supprimer(reclam);
        } else {
            delete.close();
        }
    }

    private void populateView() {
        clearGrid();
        ReclamationService S = new ReclamationService();
        List<reclamation> reclamations = S.getAll();
        int rows = 0;
        RowConstraints row = new RowConstraints();
        row.setValignment(VPos.CENTER);
        ColumnConstraints column = new ColumnConstraints();
        column.setHalignment(HPos.CENTER);
        column.setPercentWidth(30);
        for (int i = 0; i < 4; i++) {
            grid.getColumnConstraints().add(column);
        }
        grid.setAlignment(Pos.TOP_CENTER);
        grid.setStyle("-fx-box-border: transparent");
        grid.setPadding(new Insets(10, 10, 10, 10));
        grid.setVgap(10);
        grid.applyCss();
        for (reclamation reclam : reclamations) {
            grid.getRowConstraints().add(row);
            Label titre = new Label(reclam.getTitre());
            grid.add(titre, 0, rows);
            Label personne = new Label(reclam.getP().getNom_pers() + " " + reclam.getP().getPrenom_pers());
            grid.add(personne, 1, rows);
            Label time = new Label(reclam.getTimeStamp().toLocalDateTime().format(DateTimeFormatter.ofPattern("d MMM uuuu")));
            grid.add(time, 2, rows);
            Button ConsulterB = new Button();
            ConsulterB.setStyle("-fx-background-color: transparent;");
            ConsulterB.applyCss();
            FontAwesomeIconView ConsulterF = new FontAwesomeIconView(FontAwesomeIcon.EYE);
            ConsulterF.getStyleClass().add("ConsulterIcon");
            ConsulterB.setGraphic(ConsulterF);
            ConsulterB.setOnAction(value -> {
                loadPage(reclam);
            });
            grid.add(ConsulterB, 3, rows);
            Button RemoveReclam = new Button();
            RemoveReclam.setStyle("-fx-background-color: transparent;");
            FontAwesomeIconView fa = new FontAwesomeIconView(FontAwesomeIcon.TRASH);
            fa.getStyleClass().add("RemoveReclam");
            fa.autosize();
            RemoveReclam.setGraphic(fa);
            RemoveReclam.setOnAction(value -> {
                Supprimer(reclam);
            });
            grid.add(RemoveReclam, 4, rows);
            rows++;
        }
    }

    private void populateViewFilter(String type) {
        clearGrid();
        ReclamationService S = new ReclamationService();
        List<reclamation> reclamations = S.getAll();
        RowConstraints row = new RowConstraints();
        row.setValignment(VPos.CENTER);
        ColumnConstraints column = new ColumnConstraints();
        column.setHalignment(HPos.CENTER);
        column.setPercentWidth(30);
        for (int i = 0; i < 4; i++) {
            grid.getColumnConstraints().add(column);
        }
        grid.setAlignment(Pos.TOP_CENTER);
        grid.setStyle("-fx-box-border: transparent");
        grid.setPadding(new Insets(10, 10, 10, 10));
        grid.setVgap(10);
        grid.applyCss();
        int filterCounter = 0;
        for (reclamation rec : S.getAll()) {
            if (rec.getStatus().equals(type)) {
                filterCounter++;
            }
        }
        if (filterCounter == 0) {
            EmptyDatabase();
        } else {
            int rows = 0;
            for (reclamation reclam : reclamations) {
                if (type.equals("ouvert") && reclam.getStatus().equals("ouvert")) {
                    grid.getRowConstraints().add(row);
                    Label titre = new Label(reclam.getTitre());
                    grid.add(titre, 0, rows);
                    Label personne = new Label(reclam.getP().getNom_pers() + " " + reclam.getP().getPrenom_pers());
                    grid.add(personne, 1, rows);
                    Label time = new Label(reclam.getTimeStamp().toLocalDateTime().format(DateTimeFormatter.ofPattern("d MMM uuuu")));
                    grid.add(time, 2, rows);
                    Button ConsulterB = new Button();
                    ConsulterB.setStyle("-fx-background-color: transparent;");
                    ConsulterB.applyCss();
                    FontAwesomeIconView ConsulterF = new FontAwesomeIconView(FontAwesomeIcon.EYE);
                    ConsulterF.getStyleClass().add("ConsulterIcon");
                    ConsulterB.setGraphic(ConsulterF);
                    ConsulterB.setOnAction(value -> {
                        loadPage(reclam);
                    });
                    grid.add(ConsulterB, 3, rows);
                    Button RemoveReclam = new Button();
                    RemoveReclam.setStyle("-fx-background-color: transparent;");
                    FontAwesomeIconView fa = new FontAwesomeIconView(FontAwesomeIcon.TRASH);
                    fa.getStyleClass().add("RemoveReclam");
                    fa.autosize();
                    RemoveReclam.setGraphic(fa);
                    RemoveReclam.setOnAction(value -> {
                        Supprimer(reclam);
                    });
                    grid.add(RemoveReclam, 4, rows);
                    rows++;
                } else if (type.equals("cloturer") && reclam.getStatus().equals("cloturer")) {
                    grid.getRowConstraints().add(row);
                    Label titre = new Label(reclam.getTitre());
                    grid.add(titre, 0, rows);
                    Label personne = new Label(reclam.getP().getNom_pers() + " " + reclam.getP().getPrenom_pers());
                    grid.add(personne, 1, rows);
                    Label time = new Label(reclam.getTimeStamp().toLocalDateTime().format(DateTimeFormatter.ofPattern("d MMM uuuu")));
                    grid.add(time, 2, rows);
                    Button ConsulterB = new Button();
                    ConsulterB.setStyle("-fx-background-color: transparent;");
                    ConsulterB.applyCss();
                    FontAwesomeIconView ConsulterF = new FontAwesomeIconView(FontAwesomeIcon.EYE);
                    ConsulterF.getStyleClass().add("ConsulterIcon");
                    ConsulterB.setGraphic(ConsulterF);
                    ConsulterB.setOnAction(value -> {
                        loadPage(reclam);
                    });
                    grid.add(ConsulterB, 3, rows);
                    Button RemoveReclam = new Button();
                    RemoveReclam.setStyle("-fx-background-color: transparent;");
                    FontAwesomeIconView fa = new FontAwesomeIconView(FontAwesomeIcon.TRASH);
                    fa.getStyleClass().add("RemoveReclam");
                    fa.autosize();
                    RemoveReclam.setGraphic(fa);
                    RemoveReclam.setOnAction(value -> {
                        Supprimer(reclam);
                    });
                    grid.add(RemoveReclam, 4, rows);
                    rows++;
                } else if (type.equals("EnAttenteUser") && reclam.getStatus().equals("EnAttenteUser")) {
                    grid.getRowConstraints().add(row);
                    Label titre = new Label(reclam.getTitre());
                    grid.add(titre, 0, rows);
                    Label personne = new Label(reclam.getP().getNom_pers() + " " + reclam.getP().getPrenom_pers());
                    grid.add(personne, 1, rows);
                    Label time = new Label(reclam.getTimeStamp().toLocalDateTime().format(DateTimeFormatter.ofPattern("d MMM uuuu")));
                    grid.add(time, 2, rows);
                    Button ConsulterB = new Button();
                    ConsulterB.setStyle("-fx-background-color: transparent;");
                    ConsulterB.applyCss();
                    FontAwesomeIconView ConsulterF = new FontAwesomeIconView(FontAwesomeIcon.EYE);
                    ConsulterF.getStyleClass().add("ConsulterIcon");
                    ConsulterB.setGraphic(ConsulterF);
                    ConsulterB.setOnAction(value -> {
                        loadPage(reclam);
                    });
                    grid.add(ConsulterB, 3, rows);
                    Button RemoveReclam = new Button();
                    RemoveReclam.setStyle("-fx-background-color: transparent;");
                    FontAwesomeIconView fa = new FontAwesomeIconView(FontAwesomeIcon.TRASH);
                    fa.getStyleClass().add("RemoveReclam");
                    fa.autosize();
                    RemoveReclam.setGraphic(fa);
                    RemoveReclam.setOnAction(value -> {
                        Supprimer(reclam);
                    });
                    grid.add(RemoveReclam, 4, rows);
                    rows++;
                }
            }
        }
    }

    private boolean dateValidator(String Text) {
        try {
            if (Text.contains("-")) {
                DateTimeFormatter validateur = DateTimeFormatter.ofPattern("dd-MM-uuuu").withResolverStyle(ResolverStyle.STRICT);
                validateur.parse(Text);
                return true;
            } else {
                DateTimeFormatter validateur = DateTimeFormatter.ofPattern("dd/MM/uuuu").withResolverStyle(ResolverStyle.STRICT);
                validateur.parse(Text);
                return true;
            }
        } catch (DateTimeParseException e) {
            return false;
        }
    }


    /* Consulter */
    private void loadPage(reclamation reclam) {
        try {
            
            SingletonReclam.getInstance().setReclam(reclam);
            Stage thisStage = new Stage();
            FXMLLoader loader = new FXMLLoader(getClass().getResource("./Consulter/Consulter.fxml"));
            Parent root = loader.load();
            Scene scene = new Scene(root);
            thisStage.setTitle("Administration");
            thisStage.setScene(scene);
            thisStage.show();
        } catch (IOException ex) {
            System.out.println(ex.getMessage());
        }
    }
}
