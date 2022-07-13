import java.io.FileNotFoundException;
import java.io.*;
import java.util.ArrayList;
import java.util.List;
/**
 *
 * @author ASUS
 */
public class Testing {
    
    public static void main(String[] args) {
        StringBuilder sb1 = new StringBuilder();
        StringBuilder sb2 = new StringBuilder();
        StringBuilder sb3 = new StringBuilder();
        StringBuilder sb4 = new StringBuilder();
        StringBuilder sb5 = new StringBuilder();

        
        List<String> client = new ArrayList<>(); //notasi saat ini
        List<String> supplier = new ArrayList<>(); //notasi berikutnya 
        
        List<String> id = new ArrayList<>(); //diambil dari 
        List<String> name = new ArrayList<>();
        List<String> state = new ArrayList<>();

        List<String> start = new ArrayList<>();
        List<String> end = new ArrayList<>();

        String strLine;
        try {
            FileReader myActivity = new FileReader("D:\\SEMESTER 8\\SKRIPSI\\Activity Diagram\\sistem_pp.ptl");
            BufferedReader br = new BufferedReader(myActivity); //membaca perbarisnya file diagram
            
            try{ //strline baris yang di baca
                while ((strLine = br.readLine()) != null){
                    
                    if (strLine.contains("client @")){
                        String y[] = strLine.split("@");
                        client.add(y[y.length-1]);

                    } else if (strLine.contains("supplier @")){
                        String y[] = strLine.split("@");
                        supplier.add(y[y.length-1]);
                    }
                    
                    sb1.append("\n");
                    if (strLine.contains("object StateView") || strLine.contains("object DecisionView") || strLine.contains("object ActivityStateView") || strLine.contains("label(object ItemLabel)")){
                        
                        sb2.append("\n");
                        if (strLine.contains("object StateView") || strLine.contains("object DecisionView") || strLine.contains("object ActivityState")) {
                            String y[] = strLine.split("\"");

                            if (strLine.contains("object ActivityStateView") || strLine.contains("object DecisionView")){
                                
                                String r;
                                if (strLine.contains("State")){
                                    r = "ActivityState";
                                } else {
                                    r = "DecisionState";
                                }
                                
                                sb4.append("State : " + r + " Name : " + y[y.length-2] + " Id : " + y[y.length-1].substring(2));
                                System.out.println("State : " + r + " Name : " + y[y.length-2] + " Id : " + y[y.length-1].substring(2));
 
                                id.add(y[y.length-1].substring(2));
                                name.add(y[y.length-2]);
                                state.add(r);

                            } else{
                                sb4.append("State : " + y[y.length-4] + " Name : " + y[y.length-2] + " Id : " + y[y.length-1].substring(2));
                                System.out.println("State : " + y[y.length-4] + " Name : " + y[y.length-2] + " Id : " + y[y.length-1].substring(2));
                                                    
                                id.add(y[y.length-1].substring(2));
                                name.add(y[y.length-2]);
                                state.add(y[y.length-4]);

                                if (y[y.length-4].contains("EndState")){
                                    end.add(y[y.length-1].substring(2));
                                    
                                } else if(y[y.length-4].contains("StartState")){
                                    start.add(y[y.length-1].substring(2));
                                }

                                sb3.append("State : " + y[y.length-4] + "Id : " + y[y.length-1].substring(2));
                            }
                            //sb3.append("\n\n\t\tpublic " + jTextField2.getText() + "(){\n");
                                // int check = 0;
                                // int y = 0;    
                            for (int i = 0; i < name.size(); i++) {
                                sb3.append("\t\t\t" + name.get(i) + "();" + "\n");
                                }
                                sb3.append("\t\t}");
                                sb3.append(sb4.toString());
                                sb3.append("\n\n}");
                                
                                
                                AllPathsFromASource z = new AllPathsFromASource();

                                System.out.println("");
                                System.out.println("Node Pembentuk Graph");
                                sb5.append("\n======================================");
                                sb5.append("\nNode Pembentuk Graph");
                                System.out.println("");
                                
                                for (int i = 0; i < client.size(); i++) {
                                    sb5.append("\n" + client.get(i) + "->" + supplier.get(i));
                                    System.out.println(client.get(i) + "->" + supplier.get(i));
                                    z.addEdge(Integer.parseInt(client.get(i).toString()),
                                    Integer.parseInt(supplier.get(i).toString()));
                                }
                            }
                        }
                    } 
            } catch (IOException e) {
                    e.printStackTrace();
                }
        }catch (FileNotFoundException e) {
                e.printStackTrace();
            }
            System.exit(0);
        }
    }
