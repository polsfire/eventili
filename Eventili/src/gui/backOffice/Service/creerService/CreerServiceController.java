package gui.backOffice.Service.creerService;

import entities.Service;
import gui.backOffice.Service.ListerService.ListerServiceController;
import java.io.IOException;
import java.net.URL;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Optional;
import java.util.ResourceBundle;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.ButtonType;
import javafx.scene.control.TextField;
import javafx.scene.control.TextFormatter;
import javafx.scene.layout.AnchorPane;
import javafx.scene.text.Text;
import javafx.stage.Stage;
import services.ServiceService;

/**
 * FXML Controller class
 *
 * @author HP
 */
public class CreerServiceController implements Initializable {

    @FXML
    private AnchorPane screencateg;
    @FXML
    private Text text1;
    @FXML
    private Text text2;
    @FXML
    private TextField nom;
    @FXML
    private Button annuler;
    @FXML
    private Button enregistrer;
    
    ListerServiceController lst= new ListerServiceController();
    ServiceService c= new ServiceService();
//------------------------------------------------------------------------------
    @Override
    public void initialize(URL url, ResourceBundle rb) {
        nom.setTextFormatter(textFormatter);
    }
//------------------------------------------------------------------------------
    TextFormatter<String> textFormatter = new TextFormatter<>(change -> {
        String newText = change.getControlNewText();
        if (newText.length() > 20) {
            return null;
        }
        return change;
    });
//------------------------------------------------------------------------------

    @FXML
    private void close(ActionEvent event) {
        Alert a = new Alert(Alert.AlertType.CONFIRMATION);
        a.setTitle("Attention");
        a.setContentText("Etes-vous sûr de vouloir éffectuer cette action ?");
        a.setHeaderText("Attention !");
        Optional<ButtonType> action = a.showAndWait();
        if (action.get() == ButtonType.OK) {
            Stage stage = (Stage) annuler.getScene().getWindow();
            stage.close();
        }
    }
//------------------------------------------------------------------------------
    @FXML
    private void creerService(ActionEvent event) throws SQLException, IOException {
//        String n;
//        ServiceService ss = new ServiceService();
//        if (!nom.getText().isEmpty()) {
//            System.out.println("ddddddddddddddddd");
//            Service ser=c.findName(nom.getText());
//            if(!nom.getText().equals(ser.getNom_service()))
//            {
//                
//                System.out.println("hhhhhhhhhhhhhhhhhhhh");
//            n = nom.getText();
//            Service s = new Service(n);
//            ss.ajouter(s);
//            //popup lors de la creation
//            Alert a = new Alert(Alert.AlertType.INFORMATION);
//            a.setTitle("Bravo");
//            a.setContentText(" Votre service est crée");
//            a.setHeaderText("Félicitation !");
//            Optional<ButtonType> action = a.showAndWait();
//            if (action.get() == ButtonType.OK) {
//                Stage stage = (Stage) annuler.getScene().getWindow();
//                stage.close();
//            }
//            lst.refresh();
//            Stage stage = (Stage) enregistrer.getScene().getWindow();
//            stage.close();
//         
//            }
//            else{
//               
//            Alert a = new Alert(Alert.AlertType.INFORMATION);
//            a.setTitle("Attention");
//            a.setContentText(" ce service existe déja ");
//            a.setHeaderText("Attention!");
//            Optional<ButtonType> action = a.showAndWait();
//            if (action.get() == ButtonType.OK) {
////                Stage stage = (Stage) annuler.getScene().getWindow();
////                stage.close();
//            }
//            }
//        } else {
//            nom.setStyle("-fx-border-color: red;");
//        }
         String n;
        n = nom.getText();
        ArrayList<Service> al=(ArrayList<Service>) c.getAll();
        boolean x = false;
        if (!n.isEmpty()) {
            for (Service v : al) {
                if (v.getNom_service().toUpperCase().compareTo(n.toUpperCase()) == 0) {
                    x = true;
                }
            }
            if (x) {
                Alert a = new Alert(Alert.AlertType.ERROR);
                a.setTitle("Erreur");
                a.setContentText("ce service existe déja!");
                a.setHeaderText("Attention !");
                a.showAndWait();
            } else {
                 Service se= new Service(n);
                c.ajouter(se);
                lst.refresh();
                Stage stage = (Stage) enregistrer.getScene().getWindow();
                stage.close();
            }

        } else {
            Alert a = new Alert(Alert.AlertType.ERROR);
            a.setTitle("Erreur");
            a.setContentText("Veuillez saisir le nom du service");
            a.setHeaderText("Attention !");
            a.showAndWait();
        }
    }
    public void getController(ListerServiceController l)
    {
        lst=l;
    }
       
}
